<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['user']['role'] ?? 'adherent';

// Statistiques principales
$counts = $pdo->query("
    SELECT
        (SELECT COUNT(*) FROM livre WHERE nb_disponible > 0) AS livres_disponibles,
        (SELECT COUNT(*) FROM adherent) AS adherents,
        (SELECT COUNT(*) FROM emprunt WHERE date_retour_effective IS NULL) AS emprunts_en_cours,
        (SELECT COUNT(*) FROM emprunt WHERE date_retour_effective IS NULL AND date_retour_prevue < CURDATE()) AS emprunts_en_retard
")->fetch(PDO::FETCH_ASSOC);

// Emprunts par mois (année en cours)
$stmt = $pdo->query("
    SELECT MONTH(date_emprunt) AS mois, COUNT(*) AS total
    FROM emprunt
    WHERE YEAR(date_emprunt) = YEAR(CURDATE())
    GROUP BY mois
    ORDER BY mois
");
$data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$moisLabels = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
$empruntsParMois = [];
for ($i = 1; $i <= 12; $i++) {
    $empruntsParMois[] = isset($data[$i]) ? (int)$data[$i] : 0;
}

// Répartition des livres par catégorie
$stmtCat = $pdo->query("
    SELECT c.libelle, COUNT(l.id_livre) AS total
    FROM categorie c
    JOIN livre l ON l.id_categorie = c.id_categorie
    GROUP BY c.id_categorie
    HAVING total > 0
    ORDER BY total DESC
");
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

$catLabels = array_column($categories, 'libelle');
$catData   = array_column($categories, 'total');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <script src="../assets/js/theme.js" defer></script>
</head>
<body>
  <!-- Barre supérieure -->
  <header class="topbar">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
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
      <li><a href="dashboard.php" class="active">Tableau de bord</a></li>
      <?php if ($role === 'adherent'): ?>
        <li><a href="../livres/liste.php">Catalogue</a></li>
        <li><a href="../emprunts/historique.php">Mes emprunts</a></li>
        <li><a href="../emprunts/mes_reservations.php">Mes réservations</a></li>
      <?php elseif ($role === 'bibliothecaire'): ?>
        <li><a href="../livres/ajout.php">Ajouter un livre</a></li>
        <li><a href="../reservations/retour.php">Gérer les retours</a></li>
        <li><a href="../reservations/liste.php">Voir les réservations</a></li>
      <?php elseif ($role === 'admin'): ?>
        <li><a href="../utilisateurs/ajout_user">Ajouter un utilisateur</a></li>
      <?php endif; ?>
    </ul>
  </div>

  <!-- Contenu principal -->
  <div class="main-content">
    <h1>📊 Tableau de bord</h1>

    <div class="stats-grid">
      <div class="stat-card green">
        <h3>📚 Livres disponibles</h3>
        <p><?= e($counts['livres_disponibles']) ?></p>
      </div>
      <div class="stat-card blue">
        <h3>👤 Adhérents</h3>
        <p><?= e($counts['adherents']) ?></p>
      </div>
      <div class="stat-card yellow">
        <h3>📖 Emprunts en cours</h3>
        <p><?= e($counts['emprunts_en_cours']) ?></p>
      </div>
      <div class="stat-card red">
        <h3>⏰ Emprunts en retard</h3>
        <p><?= e($counts['emprunts_en_retard']) ?></p>
      </div>
    </div>

    <div class="chart-section">
      <h2>📊 Répartition des livres par catégorie</h2>
      <canvas id="livresCategorieChart"></canvas>
    </div>

    <div class="chart-section">
      <h2>📉 Évolution mensuelle des emprunts</h2>
      <canvas id="empruntsLineChart"></canvas>
    </div>
  </div>

  <!-- Chart.js local -->
  <script src="../assets/js/chart.min.js"></script>
  <script>
    // Données PHP → JS
    const catLabels = <?= json_encode($catLabels) ?>;
    const catData   = <?= json_encode($catData) ?>;

    const moisLabels = <?= json_encode($moisLabels) ?>;
    const empruntsParMois = <?= json_encode($empruntsParMois) ?>;

    const catColors = [
      '#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6610f2',
      '#fd7e14','#20c997','#6f42c1','#e83e8c','#198754','#0dcaf0'
    ];

    // Pie chart
    const ctxCat = document.getElementById('livresCategorieChart').getContext('2d');
    new Chart(ctxCat, {
      type: 'pie',
      data: {
        labels: catLabels,
        datasets: [{
          data: catData,
          backgroundColor: catColors
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { labels: { color: getComputedStyle(document.body).getPropertyValue('--text') } }
        }
      }
    });

    // Line chart
    const ctxLine = document.getElementById('empruntsLineChart').getContext('2d');
    new Chart(ctxLine, {
      type: 'line',
      data: {
        labels: moisLabels,
        datasets: [{
          label: "Emprunts",
          data: empruntsParMois,
          borderColor: '#0dcaf0',
          backgroundColor: 'rgba(13, 110, 253, 0.15)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#0dcaf0',
          pointBorderColor: '#0dcaf0'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: getComputedStyle(document.body).getPropertyValue('--text') } },
          y: { ticks: { color: getComputedStyle(document.body).getPropertyValue('--text') }, beginAtZero: true }
        }
      }
    });

    // Sidebar toggle
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }
  </script>
</body>
</html>
