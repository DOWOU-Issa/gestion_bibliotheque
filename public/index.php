<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['user'])) {
    header('Location: public/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accueil - Bibliothèque Universitaire</title>
  <link rel="stylesheet" href="../assets/css/index.css">
  <link rel="stylesheet" href="../assets/css/chatbot.css"> <!-- Styles du chatbot -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Header -->
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

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Découvrez le Savoir Infini</h1>
      <p>Explorez notre collection de livres, réservez vos lectures préférées et suivez vos emprunts en toute simplicité. Une bibliothèque moderne pour les esprits curieux.</p>
      <div class="hero-buttons">
        <a href="login.php" class="btn primary">Se connecter</a>
        <a href="../utilisateurs/inscription.php" class="btn secondary">Créer un compte</a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="container">
      <h2>Pourquoi Choisir Notre Bibliothèque ?</h2>
      <div class="features-grid">
        <div class="feature-item">
          <div class="feature-icon">📚</div>
          <h3>Collection Étendue</h3>
          <p>Accédez à des milliers de livres dans toutes les disciplines.</p>
        </div>
        <div class="feature-item">
          <div class="feature-icon">🔍</div>
          <h3>Recherche Facile</h3>
          <p>Trouvez rapidement le livre que vous cherchez.</p>
        </div>
        <div class="feature-item">
          <div class="feature-icon">📅</div>
          <h3>Réservation Simple</h3>
          <p>Réservez vos livres en ligne et récupérez-les facilement.</p>
        </div>
        <div class="feature-item">
          <div class="feature-icon">📊</div>
          <h3>Suivi des Emprunts</h3>
          <p>Gérez vos emprunts et retours depuis votre tableau de bord.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>🏛️ Bibliothèque Universitaire</h3>
          <p>Votre porte d'entrée vers le savoir.</p>
        </div>
        <div class="footer-section">
          <h3>Liens Utiles</h3>
          <ul>
            <li><a href="infos.php">À propos</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="login.php">Connexion</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Contact</h3>
          <p>Email: contact@bibliotheque.edu</p>
          <p>Téléphone: +228 93 80 34 98 </p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Bibliothèque Universitaire. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <!-- Chatbot Icon & Container -->
  <div id="chatbot-icon">🤖</div>
  <div id="chatbot-container">
    <div id="chatbot-header">Assistant Bibliothèque <span id="chatbot-close">✖</span></div>
    <div id="chatbot-messages"></div>
    <div class="chatbot-input-area">
      <input type="text" id="chatbot-input" placeholder="Posez votre question...">
      <button id="chatbot-send">Envoyer</button>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../assets/js/chatbot.js"></script>
</body>
</html>
