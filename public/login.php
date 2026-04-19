<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $err = 'Email et mot de passe requis.';
    } else {
        $stmt = $pdo->prepare("SELECT id_adherent AS id, nom, prenom, email, mot_de_passe AS password_hash, role 
                               FROM adherent 
                               WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'adherent',
            ];
            header('Location: dashboard.php');
            exit;
        } else {
            $err = 'Identifiants invalides.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion - Bibliothèque</title>
  <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
<div class="hero-background"></div>

<header class="header">
  <div class="container">
    <div class="logo">
      <h2>🏛️ Bibliothèque Universitaire</h2>
    </div>
    <nav class="nav">
      <a href="index.php" class="nav-link active">Accueil</a>
      <a href="infos.php" class="nav-link">À propos</a>
      <a href="contact.php" class="nav-link">Contact</a>
      <a href="login.php" class="nav-link">Connexion</a>
    </nav>
  </div>
</header>


<section class="login"></section>
  <div class="login-wrapper">
    <div class="login-box">
      <h2> Connexion</h2>
      <?php if ($err): ?>
        <div class="alert"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>
      <form method="post" action="">
        <label for="email">Messagerie électronique</label>
        <input type="email" name="email" id="email" placeholder="Votre email" required>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" placeholder="Votre mot de passe" required>

        <button type="submit">Se connecter</button>
      </form>
      <p class="signup-link">Pas encore inscrit ? <a href="../utilisateurs/inscription.php">Créer un compte</a></p>
    </div>
  </div>
</body>
</html>
