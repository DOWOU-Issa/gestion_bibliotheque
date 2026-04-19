<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Récupérer toutes les réservations avec infos livre + adhérent
$stmt = $pdo->query("SELECT r.*, l.titre, l.isbn, l.nb_disponible, 
                            c.libelle AS categorie, a.nom, a.prenom
                     FROM reservation r
                     JOIN livre l ON l.id_livre = r.id_livre
                     LEFT JOIN categorie c ON l.id_categorie = c.id_categorie
                     JOIN adherent a ON a.id_adherent = r.id_adherent
                     ORDER BY r.date_reservation ASC");
$reservations = $stmt->fetchAll();
?>
<div class="container mt-4">
  <h2>Gestion des réservations</h2>
  <?php if (!$reservations): ?>
    <p>Aucune réservation en attente.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Livre</th>
          <th>ISBN</th>
          <th>Catégorie</th>
          <th>Adhérent</th>
          <th>Date réservation</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservations as $r): ?>
          <tr>
            <td><?= e($r['titre']) ?></td>
            <td><?= e($r['isbn']) ?></td>
            <td><?= $r['categorie'] ? e($r['categorie']) : '<span class="text-muted">—</span>' ?></td>
            <td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td>
            <td><?= e($r['date_reservation']) ?></td>
            <td><?= e($r['statut']) ?></td>
            <td>
              <?php if ($r['statut'] === 'en_attente'): ?>
                <!-- Bouton Notifier -->
                <a href="notifier.php?id=<?= $r['id_reservation'] ?>" 
                   class="btn btn-info btn-sm">Notifier</a>

                <!-- Bouton Attribuer (seulement si le livre est dispo) -->
                <?php if ($r['nb_disponible'] > 0): ?>
                  <a href="attribuer.php?id=<?= $r['id_reservation'] ?>" 
                     class="btn btn-success btn-sm">Attribuer</a>
                <?php endif; ?>

              <?php elseif ($r['statut'] === 'notifiee'): ?>
                <span class="text-warning">En attente de retrait</span>
                <?php if ($r['nb_disponible'] > 0): ?>
                  <a href="attribuer.php?id=<?= $r['id_reservation'] ?>" 
                     class="btn btn-success btn-sm">Attribuer</a>
                <?php endif; ?>

              <?php elseif ($r['statut'] === 'attribuee'): ?>
                <span class="text-success">Attribuée</span>

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
