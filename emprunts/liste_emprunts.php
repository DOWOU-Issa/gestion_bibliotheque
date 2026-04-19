<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('adherent');

// Récupérer les emprunts en cours de l’adhérent connecté
$stmt = $pdo->prepare("SELECT e.*, l.titre 
                       FROM emprunt e
                       JOIN livre l ON l.id_livre = e.id_livre
                       WHERE e.id_adherent = :id 
                       AND e.date_retour_effective IS NULL
                       ORDER BY e.date_emprunt ASC");
$stmt->execute(['id' => $_SESSION['user']['id']]);
$emprunts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <h2>Mes emprunts en cours</h2>
  <?php if (!$emprunts): ?>
    <p>Vous n’avez aucun emprunt en cours.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Livre</th>
          <th>Date emprunt</th>
          <th>Date retour prévue</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($emprunts as $e): ?>
          <tr>
            <td><?= e($e['titre']) ?></td>
            <td><?= e($e['date_emprunt']) ?></td>
            <td><?= $e['date_retour_prevue'] ? e($e['date_retour_prevue']) : '<span class="text-muted">—</span>' ?></td>
            <td><?= e($e['statut']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
