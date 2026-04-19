<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('adherent');

$stmt = $pdo->prepare("SELECT e.*, l.titre 
                       FROM emprunt e 
                       LEFT JOIN livre l ON l.id_livre = e.id_livre 
                       WHERE e.id_adherent = :id 
                       ORDER BY e.date_emprunt DESC");
$stmt->execute(['id'=>$_SESSION['user']['id']]);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Historique des emprunts</title>
  <link rel="stylesheet" href="../assets/css/historique.css">
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
      <li><a href="historique.php" class="active">Mes emprunts</a></li>
      <li><a href="../emprunts/mes_reservations.php">Mes réservations</a></li>
    </ul>
  </div>

  <!-- Contenu principal -->
  <div class="main-content">
    <div class="historique-card">
      <h2>📖 Historique des emprunts</h2>

      <?php if (!$rows): ?>
        <p class="muted">Pas d'historique.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Titre</th>
              <th>Date emprunt</th>
              <th>Date retour prévue</th>
              <th>Date retour effective</th>
              <th>Statut</th>
              <th>Pénalité</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= e($r['titre']) ?></td>
              <td><?= e($r['date_emprunt']) ?></td>
              <td><?= $r['date_retour_prevue'] ? e($r['date_retour_prevue']) : '<span class="muted">—</span>' ?></td>
              <td><?= $r['date_retour_effective'] ? e($r['date_retour_effective']) : '<span class="muted">—</span>' ?></td>
              <td><?= e($r['statut']) ?></td>
              <td><?= number_format($r['penalite'], 2) ?> FCFA</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
