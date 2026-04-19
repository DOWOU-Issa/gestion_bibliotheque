<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
$userId = current_user_id();
$err = $_GET['err'] ?? '';
$msg = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nom === '' || $prenom === '' || $email === '') {
        $err = 'Nom, prénom et email sont requis.';
    } else {
        $params = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'id' => $userId
        ];

        $sql = "UPDATE adherent 
                SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone";

        // Gestion du mot de passe
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", mot_de_passe=:hash";
            $params['hash'] = $hash;
        }

        // Gestion de la photo
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (in_array($ext, $allowed)) {
                $uploadDir = __DIR__ . '/../assets/images/profils/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = 'profil_' . $userId . '.' . $ext;
                $filePath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
                    $sql .= ", photo=:photo";
                    $params['photo'] = $fileName;
                    $_SESSION['user']['photo'] = $fileName;
                }
            }
        }

        $sql .= " WHERE id_adherent=:id";
        $pdo->prepare($sql)->execute($params);

        $msg = 'Profil mis à jour.';
        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['prenom'] = $prenom;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['telephone'] = $telephone;
    }
}

$stmt = $pdo->prepare("SELECT nom, prenom, email, telephone, photo 
                       FROM adherent WHERE id_adherent = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();
$photo = $user['photo'] ?? 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon profil</title>
  <link rel="stylesheet" href="../assets/css/profil.css">
  <script src="../assets/js/theme.js" defer></script>
</head>
<body>
  <header class="topbar">
    <a href="../public/dashboard.php" class="back-arrow">⬅️</a>
    <div class="settings">
      <div class="dropdown">
        <button class="settings-btn">⚙️</button>
        <div class="dropdown-content">
          <a href="#">👤 Profil</a>
          <a href="../public/logout.php">🚪 Déconnexion</a>
        </div>
      </div>
    </div>
  </header>

  <div class="profil-wrapper">
    <div class="profil-card">
      <h2>👤 Profil</h2>

      <form method="post" enctype="multipart/form-data" class="profil-form">
        <div class="photo-section">
          <img src="../assets/images/profils/<?= e($photo) ?>" 
               alt="Photo de profil" class="profil-photo" id="preview">
          <label for="photo" class="btn-upload">📷</label>
          <input type="file" id="photo" name="photo" accept="image/*" 
                 onchange="previewPhoto(event)" style="display:none">
        </div>

        <label>Nom</label>
        <input name="nom" value="<?= e($user['nom']) ?>" required>

        <label>Prénom</label>
        <input name="prenom" value="<?= e($user['prenom']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>" required>

        <label>Téléphone</label>
        <input type="text" name="telephone" value="<?= e($user['telephone'] ?? '') ?>">

        <label>Nouveau mot de passe</label>
        <input type="password" name="password" placeholder="Laisser vide pour garder">

        <button type="submit" class="btn-submit">Mettre à jour</button>
      </form>
    </div>
  </div>

  <script>
    function previewPhoto(event) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('preview').src = e.target.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>
</html>
