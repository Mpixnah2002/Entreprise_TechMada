<?php /** Minimal RH demandes list view */ ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Demandes RH</title>
  <style>table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:6px}</style>
</head>
<body>
  <h1>Demandes</h1>
  <?php if(session()->getFlashdata('error')): ?>
    <div style="color:#b00"><?=esc(session()->getFlashdata('error'))?></div>
  <?php endif; ?>
  <?php if(session()->getFlashdata('message')): ?>
    <div style="color:#080"><?=esc(session()->getFlashdata('message'))?></div>
  <?php endif; ?>

  <table>
    <thead>
      <tr><th>ID</th><th>Employé</th><th>Type</th><th>Période</th><th>Nb jours</th><th>Solde restant</th><th>Statut</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach($demandes as $d): ?>
      <tr>
        <td><?=esc($d['id'])?></td>
        <td><?=esc($d['employe_nom'])?></td>
        <td><?=esc($d['type_libelle'])?></td>
        <td><?=esc($d['date_debut'])?> → <?=esc($d['date_fin'])?></td>
        <td style="text-align:center"><?=esc($d['nb_jours'])?></td>
        <td style="text-align:center"><?=esc($d['solde_restant'])?></td>
        <td><?=esc($d['statut'])?></td>
        <td>
          <?php if($d['statut']=='en_attente'): ?>
            <form method="post" action="<?=site_url('rh/approuver/'.$d['id'])?>" style="display:inline">
              <input type="text" name="commentaire" placeholder="Commentaire (optionnel)" />
              <button type="submit">Approuver</button>
            </form>
            <form method="post" action="<?=site_url('rh/refuser/'.$d['id'])?>" style="display:inline">
              <input type="text" name="commentaire" placeholder="Motif (optionnel)" />
              <button type="submit">Refuser</button>
            </form>
          <?php else: ?>
            <?=esc($d['statut'])?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
