<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nettoyage des données
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Vérifie si l'utilisateur est connecté
function require_login() {
    if (empty($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        header('Location: ../public/login.php');
        exit;
    }
}

// Récupère l'ID de l'utilisateur connecté
function current_user_id() {
    return $_SESSION['user']['id'] ?? null;
}

// Échappe les caractères spéciaux pour éviter les injections XSS
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Messages flash
function flash_set($key, $message) {
    $_SESSION['_flash'][$key] = $message;
}

function flash_get($key) {
    if (isset($_SESSION['_flash'][$key])) {
        $message = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $message;
    }
    return null;
}

// Vérifie si l'utilisateur a un rôle spécifique
function require_role(...$roles) {
    require_login();
    $user_role = $_SESSION['user']['role'] ?? null;
    if (!in_array($user_role, $roles, true)) {
        http_response_code(403);
        echo "<h1>403 - Accès refusé</h1>";
        echo "<p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
        exit;
    }
}
