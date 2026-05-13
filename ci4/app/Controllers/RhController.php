<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DemandeCongeModel;
use App\Models\UserModel;
use App\Models\TypeCongeModel;
use App\Models\SoldeModel;
use CodeIgniter\Database\ConnectionInterface;

class RhController extends BaseController
{
    protected $demandeModel;
    protected $userModel;
    protected $typeModel;
    protected $soldeModel;
    protected $db;

    public function __construct()
    {
        $this->demandeModel = new DemandeCongeModel();
        $this->userModel = new UserModel();
        $this->typeModel = new TypeCongeModel();
        $this->soldeModel = new SoldeModel();
        $this->db = \Config\Database::connect();
    }

    // List demandes en attente with basic filters
    public function index()
    {
        $dept = $this->request->getGet('departement');
        $statut = $this->request->getGet('statut') ?? 'en_attente';

        $builder = $this->demandeModel;
        $where = [];
        if ($statut) {
            $where['statut'] = $statut;
        }
        if ($dept) {
            // join via employe departement
            $builder = $this->demandeModel->select('conges.*, employes.nom as employe_nom, employes.departement_id, types_conge.libelle as type_libelle')
                ->join('employes', 'employes.id = conges.employe_id')
                ->join('types_conge', 'types_conge.id = conges.type_conge_id')
                ->where('employes.departement_id', $dept);
        } else {
            $builder = $this->demandeModel->select('conges.*, employes.nom as employe_nom, employes.departement_id, types_conge.libelle as type_libelle')
                ->join('employes', 'employes.id = conges.employe_id')
                ->join('types_conge', 'types_conge.id = conges.type_conge_id');
        }

        if ($where) {
            $builder->where($where);
        }

        $demandes = $builder->orderBy('conges.created_at', 'DESC')->findAll();

        // fetch solde for each demande's employe/type
        foreach ($demandes as &$d) {
            $solde = $this->soldeModel->where(['employe_id' => $d['employe_id'], 'type_conge_id' => $d['type_conge_id']])->first();
            $d['solde_restant'] = $solde['solde_restant'] ?? 0;
        }

        return view('rh/list', ['demandes' => $demandes]);
    }

    // Approve a demande (transactional)
    public function approve($id = null)
    {
        $id = (int)$id;
        $demande = $this->demandeModel->find($id);
        if (!$demande) {
            return redirect()->back()->with('error', 'Demande introuvable');
        }

        if ($demande['statut'] !== DemandeCongeModel::STATUT_ATTENTE) {
            return redirect()->back()->with('error', 'Demande déjà traitée');
        }

        $nb = (int)$demande['nb_jours'];
        $solde = $this->soldeModel->where(['employe_id' => $demande['employe_id'], 'type_conge_id' => $demande['type_conge_id']])->first();
        $soldeRestant = $solde['solde_restant'] ?? 0;

        if ($soldeRestant < $nb) {
            return redirect()->back()->with('error', 'Solde insuffisant');
        }

        $comment = $this->request->getPost('commentaire');
        $rhId = session('user_id') ?? null;

        $trans = $this->db->transStart();
        try {
            // update demande
            $this->demandeModel->update($id, [
                'statut' => DemandeCongeModel::STATUT_APPROUVE,
                'valide_par' => $rhId,
                'commentaire' => $comment,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // update solde
            $this->soldeModel->where(['employe_id' => $demande['employe_id'], 'type_conge_id' => $demande['type_conge_id']])->set([
                'solde_restant' => 'solde_restant - ' . $nb,
                'solde_pris' => 'solde_pris + ' . $nb,
            ])->update();

            $this->db->transComplete();
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors de l\'approbation');
        }

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Transaction échouée');
        }

        return redirect()->back()->with('message', 'Demande approuvée');
    }

    // Refuse a demande
    public function refuse($id = null)
    {
        $id = (int)$id;
        $demande = $this->demandeModel->find($id);
        if (!$demande) {
            return redirect()->back()->with('error', 'Demande introuvable');
        }

        if ($demande['statut'] !== DemandeCongeModel::STATUT_ATTENTE) {
            return redirect()->back()->with('error', 'Demande déjà traitée');
        }

        $comment = $this->request->getPost('commentaire');
        $rhId = session('user_id') ?? null;

        $this->demandeModel->update($id, [
            'statut' => DemandeCongeModel::STATUT_REFUSE,
            'valide_par' => $rhId,
            'commentaire' => $comment,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('message', 'Demande refusée');
    }
}
