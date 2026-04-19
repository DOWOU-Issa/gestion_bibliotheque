<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérification du rôle admin
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit;
}

$err = '';
$success = '';

// === Ajouter un utilisateur ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $pass = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'adherent';

    if ($nom === '' || $prenom === '' || $email === '' || $pass === '' || $telephone === '') {
        $err = 'Tous les champs sont requis.';
    } else {
        $stmt = $pdo->prepare("SELECT id_adherent FROM adherent WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $err = 'Email déjà utilisé.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $date_inscription = date("Y-m-d");

            $stmt = $pdo->prepare("INSERT INTO adherent (nom, prenom, email, telephone, mot_de_passe, role, date_inscription) 
                                   VALUES (:nom, :prenom, :email, :telephone, :hash, :role, :date_inscription)");
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'hash' => $hash,
                'role' => $role,
                'date_inscription' => $date_inscription
            ]);
            $success = "Utilisateur ajouté avec succès.";
        }
    }
}

// === Supprimer un utilisateur ===
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM adherent WHERE id_adherent = :id");
    $stmt->execute(['id' => $id]);
    $success = "Utilisateur supprimé.";
}

// === Historique des utilisateurs ===
$stmt = $pdo->query("SELECT id_adherent, nom, prenom, email, telephone, role, date_inscription 
                     FROM adherent ORDER BY date_inscription DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des utilisateurs</title>
  <link rel="stylesheet" href="../assets/css/ajout_user.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="topbar-back">
  <a href="../public/dashboard.php" class="topbar-link">⬅ Retour au Dashboard</a>
</div>


<section class="ajout-user">
  <div class="container">
    <h2>👤 Gestion des utilisateurs</h2>

    <?php if ($err): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Formulaire ajout -->
    <form method="post" action="">
      <input type="hidden" name="action" value="ajouter">
      <label>Nom</label>
      <input type="text" name="nom" required>
      <label>Prénom</label>
      <input type="text" name="prenom" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Téléphone</label>
      <input type="text" name="telephone" required>
      <label>Mot de passe</label>
      <input type="password" name="password" required>
      <label>Rôle</label>
      <select name="role">
        <option value="adherent">Adhérent</option>
        <option value="bibliothecaire">Bibliothécaire</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit">Ajouter</button>
    </form>

    <!-- Historique des utilisateurs -->
    <h3 style="margin-top:2rem;">📜 Historique des utilisateurs</h3>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Rôle</th>
          <th>Date inscription</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id_adherent'] ?></td>
            <td><?= htmlspecialchars($u['nom']) ?></td>
            <td><?= htmlspecialchars($u['prenom']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['telephone']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= htmlspecialchars($u['date_inscription']) ?></td>
            <td><a href="?delete=<?= $u['id_adherent'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️ Supprimer</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
