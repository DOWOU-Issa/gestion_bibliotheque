<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$stmt = $pdo->query("SELECT * FROM auteur ORDER BY nom ASC");
$auteurs = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center">
    <h2>Liste des auteurs</h2>
    <a href="ajout.php" class="btn btn-success btn-sm">Ajouter un auteur</a>
  </div>

  <?php if (!$auteurs): ?>
    <p class="mt-3">Aucun auteur enregistré.</p>
  <?php else: ?>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Bio</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($auteurs as $a): ?>
          <tr>
            <td><?= e($a['nom']) ?></td>
            <td><?= e($a['prenom']) ?></td>
            <td><?= e($a['bio']) ?></td>
            <td>
              <a href="details.php?id=<?= $a['id_auteur'] ?>" class="btn btn-primary btn-sm">Voir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
