<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $pass = $_POST['password'] ?? '';

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
                                   VALUES (:nom, :prenom, :email, :telephone, :hash, 'adherent', :date_inscription)");
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'hash' => $hash,
                'date_inscription' => $date_inscription
            ]);

            flash_set('success', 'Inscription réussie. Connectez-vous.');
            header('Location: ../public/login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription - Bibliothèque</title>
  <link rel="stylesheet" href="../assets/css/inscription.css">
</head>
<body>
<div class="hero-background"></div>

<header class="header">
  <div class="container">
    <div class="logo">
      <h2>🏛️ Bibliothèque Universitaire</h2>
    </div>
    <nav class="nav">
      <a href="../public/index.php" class="nav-link active">Accueil</a>
      <a href="../public/infos.php" class="nav-link">À propos</a>
      <a href="../public/contact.php" class="nav-link">Contact</a>
      <a href="../public/login.php" class="nav-link">Connexion</a>
    </nav>
  </div>
</header>

<section class="inscription">
  <div class="inscription-wrapper">
    <div class="inscription-box">
      <h2> Inscription adhérent</h2>
      <?php if ($err): ?>
        <div class="alert"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>
      <form method="post">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" placeholder="Votre nom" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" placeholder="Votre prénom" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Votre email" required>

        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" placeholder="Votre numéro" required>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" placeholder="Créer un mot de passe" required>

        <button type="submit">S'inscrire</button>
      </form>
      <p class="login-link">Déjà inscrit ? <a href="../public/login.php">Se connecter</a></p>
    </div>
  </div>
</body>
</html>
