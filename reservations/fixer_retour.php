<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$id_emprunt = intval($_GET['id'] ?? 0);
if ($id_emprunt < 1) {
    flash_set('error', 'Emprunt invalide.');
    header("Location: liste_emprunts.php");
    exit();
}

// Récupérer l’emprunt
$stmt = $pdo->prepare("SELECT e.*, l.titre, a.nom, a.prenom
                       FROM emprunt e
                       JOIN livre l ON l.id_livre = e.id_livre
                       JOIN adherent a ON a.id_adherent = e.id_adherent
                       WHERE e.id_emprunt = :id");
$stmt->execute(['id' => $id_emprunt]);
$emprunt = $stmt->fetch();

if (!$emprunt) {
    flash_set('error', 'Emprunt introuvable.');
    header("Location: liste_emprunts.php");
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_retour_prevue = trim($_POST['date_retour_prevue'] ?? '');

    if (!$date_retour_prevue) {
        flash_set('error', 'Veuillez choisir une date de retour.');
    } else {
        $stmt = $pdo->prepare("UPDATE emprunt SET date_retour_prevue = :date WHERE id_emprunt = :id");
        $stmt->execute([
            'date' => $date_retour_prevue,
            'id' => $id_emprunt
        ]);
        flash_set('success', 'Date de retour prévue fixée ✅');
        header("Location: liste_emprunts.php");
        exit();
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <h2>Fixer la date de retour prévue</h2>
  <?php if ($msg = flash_get('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($msg = flash_get('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

  <p><strong>Livre :</strong> <?= e($emprunt['titre']) ?></p>
  <p><strong>Adhérent :</strong> <?= e($emprunt['prenom'] . ' ' . $emprunt['nom']) ?></p>
  <p><strong>Date d’emprunt :</strong> <?= e($emprunt['date_emprunt']) ?></p>

  <form method="post" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Date de retour prévue</label>
      <input type="date" name="date_retour_prevue" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Valider</button>
    <a href="liste_emprunts.php" class="btn btn-secondary">Annuler</a>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
