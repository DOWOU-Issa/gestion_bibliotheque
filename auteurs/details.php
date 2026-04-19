<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$id_auteur = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM auteur WHERE id_auteur = :id");
$stmt->execute(['id' => $id_auteur]);
$auteur = $stmt->fetch();

if (!$auteur) {
    flash_set('error', "Auteur introuvable ❌");
    header("Location: liste.php");
    exit();
}

// Récupérer les livres de cet auteur
$stmt = $pdo->prepare("SELECT l.* 
                       FROM livre l
                       JOIN ecrire e ON l.id_livre = e.id_livre
                       WHERE e.id_auteur = :id");
$stmt->execute(['id' => $id_auteur]);
$livres = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <h2><?= e($auteur['prenom'] . ' ' . $auteur['nom']) ?></h2>
  <p><?= nl2br(e($auteur['bio'])) ?></p>

  <h3 class="mt-4">Livres de cet auteur</h3>
  <?php if (!$livres): ?>
    <p>Aucun livre enregistré pour cet auteur.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($livres as $l): ?>
        <li><?= e($l['titre']) ?> (ISBN: <?= e($l['isbn']) ?>)</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
