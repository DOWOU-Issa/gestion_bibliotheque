<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire','admin');

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM livre WHERE id_livre = :id");
$stmt->execute(['id' => $id]);
$livre = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $annee = $_POST['annee_pub'];
    $resume = $_POST['resume'];

    $stmt = $pdo->prepare("UPDATE livre SET titre = :titre, annee_pub = :annee_pub, resume = :resume WHERE id_livre = :id");
    $stmt->execute([
        'titre' => $titre,
        'annee_pub' => $annee,
        'resume' => $resume,
        'id' => $id,
    ]);
    flash_set('success','Livre modifié avec succès.');
    header('Location: liste.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container mt-4">
  <h2>Modifier un livre</h2>
  <form method="post">
    <div class="mb-2"><label>Titre <input name="titre" class="form-control" value="<?= e($livre['titre']) ?>"></label></div>
    <div class="mb-2"><label>Année <input name="annee_pub" class="form-control" value="<?= e($livre['annee_pub']) ?>"></label></div>
    <div class="mb-2"><label>Résumé <textarea name="resume" class="form-control"><?= e($livre['resume']) ?></textarea></label></div>
    <button type="submit" class="btn btn-primary">Modifier</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
