<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="/gestion_bibliotheque/assets/css/contact.css">
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
<section class="contact">
<h2>Contactez-nous</h2>
<form method="post" action="#">
    <input type="text" name="Nom" placeholder="Votre nom" required>
    <input type="email" name="Email" placeholder="Votre email" required>
    <textarea name="message" placeholder="Votre message" required></textarea>
    <button type="submit">Envoyer</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
