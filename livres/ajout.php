<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

// Charger les catégories
$stmt = $pdo->query("SELECT * FROM categorie ORDER BY libelle ASC");
$categories = $stmt->fetchAll();

// Charger les auteurs
$stmt = $pdo->query("SELECT * FROM auteur ORDER BY nom ASC");
$auteurs = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = trim($_POST['isbn'] ?? '');
    $titre = trim($_POST['titre'] ?? '');
    $annee_pub = trim($_POST['annee_pub'] ?? '');
    $nb_exemplaires = max(1, intval($_POST['nb_exemplaires'] ?? 1));
    $resume = trim($_POST['resume'] ?? '');
    $id_categorie = !empty($_POST['id_categorie']) ? intval($_POST['id_categorie']) : null;
    $id_auteur = !empty($_POST['id_auteur']) ? intval($_POST['id_auteur']) : null;

    if ($isbn === '' || $titre === '') {
        flash_set('error', "ISBN et Titre sont obligatoires.");
    } else {
        try {
            // Insérer le livre
            $stmt = $pdo->prepare("INSERT INTO livre 
                (isbn, titre, annee_pub, nb_exemplaires, nb_disponible, resume, id_categorie) 
                VALUES (:isbn, :titre, :annee_pub, :nb_exemplaires, :nb_disponible, :resume, :id_categorie)");
            $stmt->execute([
                'isbn' => $isbn,
                'titre' => $titre,
                'annee_pub' => $annee_pub !== '' ? $annee_pub : null,
                'nb_exemplaires' => $nb_exemplaires,
                'nb_disponible' => $nb_exemplaires,
                'resume' => $resume !== '' ? $resume : null,
                'id_categorie' => $id_categorie
            ]);
            $id_livre = $pdo->lastInsertId();

            // Relier à l’auteur choisi
            if ($id_auteur) {
                $stmt = $pdo->prepare("INSERT INTO ecrire (id_auteur, id_livre) VALUES (:id_auteur, :id_livre)");
                $stmt->execute(['id_auteur' => $id_auteur, 'id_livre' => $id_livre]);
            }

            flash_set('success', "Livre ajouté avec succès ✅");
            header("Location: gestion.php");
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
  <h2>Ajouter un livre</h2>
  <?php if ($msg = flash_get('error')): ?>
    <div class="alert alert-danger"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if ($msg = flash_get('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">ISBN</label>
      <input type="text" name="isbn" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Titre</label>
      <input type="text" name="titre" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Année de publication</label>
      <input type="number" name="annee_pub" class="form-control" min="1000" max="9999">
    </div>
    <div class="mb-3">
      <label class="form-label">Nombre d’exemplaires</label>
      <input type="number" name="nb_exemplaires" class="form-control" value="1" min="1" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Résumé</label>
      <textarea name="resume" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Catégorie</label>
      <select name="id_categorie" class="form-select">
        <option value="">-- Choisir une catégorie --</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id_categorie'] ?>"><?= e($c['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Auteur</label>
      <select name="id_auteur" class="form-select">
        <option value="">-- Choisir un auteur --</option>
        <?php foreach ($auteurs as $a): ?>
          <option value="<?= $a['id_auteur'] ?>"><?= e($a['prenom'] . ' ' . $a['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Ajouter</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
