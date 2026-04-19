<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$stmt = $pdo->query("SELECT l.*, c.libelle AS categorie
                     FROM livre l
                     LEFT JOIN categorie c ON c.id_categorie = l.id_categorie
                     ORDER BY l.titre ASC");
$livres = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center">
    <h2>Gestion des livres</h2>
    <a href="ajout.php" class="btn btn-success btn-sm">Ajouter un livre</a>
  </div>

  <?php if (!$livres): ?>
    <p class="mt-3">Aucun livre dans la base.</p>
  <?php else: ?>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>Titre</th>
          <th>ISBN</th>
          <th>Année</th>
          <th>Catégorie</th>
          <th>Exemplaires</th>
          <th>Disponibles</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($livres as $l): ?>
          <tr>
            <td><?= e($l['titre']) ?></td>
            <td><?= e($l['isbn']) ?></td>
            <td><?= e($l['annee_pub']) ?></td>
            <td><?= e($l['categorie'] ?? '—') ?></td>
            <td><?= e($l['nb_exemplaires']) ?></td>
            <td>
              <?php if ($l['nb_disponible'] > 0): ?>
                <span class="badge bg-success"><?= e($l['nb_disponible']) ?></span>
              <?php else: ?>
                <span class="badge bg-danger">0</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="modifier.php?id=<?= $l['id_livre'] ?>" class="btn btn-outline-primary btn-sm">Modifier</a>
              <a href="supprimer.php?id=<?= $l['id_livre'] ?>" class="btn btn-outline-danger btn-sm"
                 onclick="return confirm('Supprimer ce livre ?');">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
