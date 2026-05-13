<?php

namespace App\Models;

use CodeIgniter\Model;

class DemandeCongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'employe_id', 'type_conge_id', 'date_debut', 'date_fin', 'nb_jours', 'motif', 'statut', 'valide_par', 'commentaire'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType = 'array';

    // Common statuses
    public const STATUT_ATTENTE = 'en_attente';
    public const STATUT_APPROUVE = 'approuvee';
    public const STATUT_REFUSE = 'refusee';
    public const STATUT_ANNULEE = 'annulee';
}
