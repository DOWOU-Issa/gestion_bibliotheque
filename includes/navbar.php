<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../public/index.php">Bibliothèque</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if(!empty($_SESSION['user'])): ?>
          
          <!-- 🔹 Menu Adhérent -->
          <?php if($_SESSION['user']['role'] === 'adherent'): ?>
            <li class="nav-item"><a class="nav-link" href="../livres/liste.php">Catalogue</a></li>
            <li class="nav-item"><a class="nav-link" href="../emprunts/liste_emprunts.php">Mes emprunts</a></li>
            <li class="nav-item"><a class="nav-link" href="../emprunts/historique.php">Historique</a></li>
            <li class="nav-item"><a class="nav-link" href="../emprunts/mes_reservations.php">Mes réservations</a></li>
          
          <!-- 🔹 Menu Bibliothécaire -->
          <?php elseif($_SESSION['user']['role'] === 'bibliothecaire'): ?>
            <li class="nav-item"><a class="nav-link" href="../livres/gestion.php">Gestion des livres</a></li>
            <li class="nav-item"><a class="nav-link" href="../auteurs/liste.php">Gestion des auteurs</a></li>
            <li class="nav-item"><a class="nav-link" href="../reservations/gestion_emprunts.php">Emprunts & Retours</a></li>
            <li class="nav-item"><a class="nav-link" href="../reservations/liste.php">Réservations</a></li>
          
          <!-- 🔹 Menu Admin -->
          <?php elseif($_SESSION['user']['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="../utilisateurs/inscription.php">Ajouter un utilisateur</a></li>
            <li class="nav-item"><a class="nav-link" href="../livres/gestion.php">Gestion des livres</a></li>
            <li class="nav-item"><a class="nav-link" href="../auteurs/liste.php">Gestion des auteurs</a></li>
            <li class="nav-item"><a class="nav-link" href="../reservations/liste.php">Réservations</a></li>
          <?php endif; ?>
        
        <?php endif; ?>
      </ul>
      
      <!-- 🔹 Menu Profil / Connexion -->
      <ul class="navbar-nav">
        <?php if(!empty($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="../utilisateurs/profil.php">Mon profil</a></li>
          <li class="nav-item"><a class="nav-link" href="../public/logout.php">Déconnexion</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../public/login.php">Connexion</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
