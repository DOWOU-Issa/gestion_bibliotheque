<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
$user = $_SESSION['user'] ?? [];
$role = $user['role'] ?? '';
$id_adherent = $user['id'] ?? null;

// Récupérer tous les livres avec leur catégorie
$stmt = $pdo->query("SELECT l.*, c.libelle AS categorie
                     FROM livre l
                     LEFT JOIN categorie c ON l.id_categorie = c.id_categorie
                     ORDER BY l.titre ASC");
$livres = $stmt->fetchAll();

// Initialiser les contrôles pour l’adhérent connecté
$reservations = [];
$emprunts = [];

if ($role === 'adherent') {
    $stmtRes = $pdo->prepare("SELECT id_livre FROM reservation 
                              WHERE id_adherent = :id AND statut IN ('en_attente','attribuee','notifiee')");
    $stmtRes->execute(['id' => $id_adherent]);
    $reservations = $stmtRes->fetchAll(PDO::FETCH_COLUMN);

    $stmtEmp = $pdo->prepare("SELECT id_livre FROM emprunt 
                              WHERE id_adherent = :id AND statut IN ('en_cours','retard')");
    $stmtEmp->execute(['id' => $id_adherent]);
    $emprunts = $stmtEmp->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Catalogue des livres</title>
  <link rel="stylesheet" href="../assets/css/catalogue.css">
  <script src="../assets/js/theme.js" defer></script>
</head>
<body>
  <!-- Barre supérieure -->
  <header class="topbar">
    <div class="settings">
      <div class="dropdown">
        <button class="settings-btn">⚙️</button>
        <div class="dropdown-content">
          <a href="../utilisateurs/profil.php">👤 Profil</a>
          <a href="#" onclick="toggleTheme()">🌓 Thème</a>
          <a href="../logout.php">🚪 Déconnexion</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Menu Bibliothèque</h2>
    <ul>
      <li><a href="../public/dashboard.php">Tableau de bord</a></li>
      <li><a href="liste.php" class="active">Catalogue</a></li>
      <li><a href="../emprunts/historique.php">Mes emprunts</a></li>
      <li><a href="../reservations/mes_reservations.php">Mes réservations</a></li>
    </ul>
  </div>

  <!-- Contenu -->
  <div class="catalogue-wrapper">
    <div class="catalogue-card">
      <h2> Catalogue des livres</h2>

      <!-- Barre de recherche -->
      <input type="text" id="searchInput" placeholder="Rechercher un livre..." class="search-bar">

      <?php if (!$livres): ?>
        <p>Aucun livre disponible.</p>
      <?php else: ?>
        <table id="livreTable">
          <thead>
            <tr>
              <th>Titre</th>
              <th>ISBN</th>
              <th>Catégorie</th>
              <th>Disponibles</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($livres as $livre): ?>
              <?php
                $id_livre = $livre['id_livre'];
                $a_emprunte = in_array($id_livre, $emprunts);
                $a_reserve = in_array($id_livre, $reservations);
              ?>
              <tr>
                <td><?= e($livre['titre']) ?></td>
                <td><?= e($livre['isbn']) ?></td>
                <td><?= $livre['categorie'] ? e($livre['categorie']) : '—' ?></td>
                <td><?= e($livre['nb_disponible']) ?></td>
                <td>
                  <?php if ($role === 'adherent'): ?>
                    <?php if ($a_emprunte): ?>
                      <button class="btn disabled">Déjà emprunté</button>
                    <?php elseif ($a_reserve): ?>
                      <button class="btn disabled">Déjà réservé</button>
                    <?php elseif ($livre['nb_disponible'] > 0): ?>
                      <a href="../emprunts/emprunter.php?id=<?= $id_livre ?>" class="btn primary">Emprunter</a>
                    <?php else: ?>
                      <a href="../emprunts/reserver.php?id=<?= $id_livre ?>" class="btn warning">Réserver</a>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="muted">Connectez-vous pour emprunter</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Script recherche dynamique -->
  <script>
    document.getElementById('searchInput').addEventListener('input', function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#livreTable tbody tr');

      rows.forEach(row => {
        const titre = row.cells[0].textContent.toLowerCase();
        const categorie = row.cells[2].textContent.toLowerCase();
        row.style.display = (titre.includes(filter) || categorie.includes(filter)) ? '' : 'none';
      });
    });
  </script>
</body>
</html>
