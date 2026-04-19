<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

// Récupérer tous les emprunts
$stmt = $pdo->query("SELECT e.*, l.titre, a.nom, a.prenom
                     FROM emprunt e
                     JOIN livre l ON l.id_livre = e.id_livre
                     JOIN adherent a ON a.id_adherent = e.id_adherent
                     ORDER BY e.date_emprunt DESC");
$emprunts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <h2>Gestion des emprunts</h2>
  <?php if (!$emprunts): ?>
    <p>Aucun emprunt enregistré.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Livre</th>
          <th>Adhérent</th>
          <th>Date emprunt</th>
          <th>Date retour prévue</th>
          <th>Date retour effective</th>
          <th>Statut</th>
          <th>Pénalité</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($emprunts as $e): ?>
          <tr>
            <td><?= e($e['titre']) ?></td>
            <td><?= e($e['prenom'] . ' ' . $e['nom']) ?></td>
            <td><?= e($e['date_emprunt']) ?></td>
            <td><?= e($e['date_retour_prevue']) ?></td>
            <td><?= $e['date_retour_effective'] ? e($e['date_retour_effective']) : '<span class="text-muted">—</span>' ?></td>
            <td><?= e($e['statut']) ?></td>
            <td><?= e($e['penalite']) ?> FCFA</td>
            <td>
              <?php if ($e['statut'] === 'en_cours' || $e['statut'] === 'retard'): ?>
                <a href="retour.php?id=<?= $e['id_emprunt'] ?>" 
                   class="btn btn-primary btn-sm"
                   onclick="return confirm('Confirmer le retour de ce livre ?');">
                   Enregistrer retour
                </a>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
