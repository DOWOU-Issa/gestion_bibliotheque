<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($nom === '') {
        flash_set('error', "Le nom de l'auteur est obligatoire.");
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO auteur (nom, prenom, bio) VALUES (:nom, :prenom, :bio)");
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom !== '' ? $prenom : null,
                'bio' => $bio !== '' ? $bio : null
            ]);
            flash_set('success', "Auteur ajouté avec succès ✅");
            header("Location: liste.php");
            exit();
        } catch (PDOException $e) {
            flash_set('error', "Erreur lors de l’ajout: " . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <h2>Ajouter un auteur</h2>
  <?php if ($msg = flash_get('error')): ?>
    <div class="alert alert-danger"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if ($msg = flash_get('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nom</label>
      <input type="text" name="nom" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Prénom</label>
      <input type="text" name="prenom" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Bio</label>
      <textarea name="bio" class="form-control" rows="4"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Ajouter</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
