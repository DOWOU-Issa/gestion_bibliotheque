<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('adherent');

// Récupérer les réservations de l’adhérent connecté
$stmt = $pdo->prepare("SELECT r.*, l.titre 
                       FROM reservation r
                       JOIN livre l ON l.id_livre = r.id_livre
                       WHERE r.id_adherent = :id
                       ORDER BY r.date_reservation DESC");
$stmt->execute(['id' => $_SESSION['user']['id']]);
$reservations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes réservations</title>
  <link rel="stylesheet" href="../assets/css/mes_reservations.css">
  <script src="../assets/js/theme.js" defer></script>
</head>
<body>
  <!-- Barre supérieure -->
  <header class="topbar">
    <button class="toggle-btn" onclick="toggleSidebar()"></button>
    <div class="settings">
      <div class="dropdown">
        <button class="settings-btn">⚙️</button>
        <div class="dropdown-content">
          <a href="../utilisateurs/profil.php">👤 Profil</a>
          <a href="#" onclick="toggleTheme()">🌓 Thème</a>
          <a href="../public/logout.php">🚪 Déconnexion</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h2>📚 Bibliothèque</h2>
    <ul>
      <li><a href="../public/dashboard.php">Tableau de bord</a></li>
      <li><a href="../livres/liste.php">Catalogue</a></li>
      <li><a href="../emprunts/historique.php">Mes emprunts</a></li>
      <li><a href="mes_reservations.php" class="active">Mes réservations</a></li>
    </ul>
  </div>

  <!-- Contenu principal -->
  <div class="main-content">
    <div class="reservations-card">
      <h2>📖 Mes réservations</h2>

      <?php if (!$reservations): ?>
        <p class="muted">Vous n’avez aucune réservation.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Livre</th>
              <th>Date réservation</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservations as $r): ?>
              <tr>
                <td><?= e($r['titre']) ?></td>
                <td><?= e($r['date_reservation']) ?></td>
                <td><?= e($r['statut']) ?></td>
                <td>
                  <?php if ($r['statut'] === 'en_attente'): ?>
                    <a href="annuler_reservation.php?id=<?= $r['id_reservation'] ?>" 
                       class="btn danger"
                       onclick="return confirm('Annuler cette réservation ?');">
                       Annuler
                    </a>
                  <?php else: ?>
                    <span class="muted">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
