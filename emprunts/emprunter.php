<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('adherent');

// Récupérer l’ID du livre
$id_livre = intval($_GET['id'] ?? 0);
if ($id_livre < 1) {
    flash_set('error', "Livre invalide.");
    header("Location: ../livres/liste.php");
    exit();
}

// Vérifier disponibilité
$stmt = $pdo->prepare("SELECT * FROM livre WHERE id_livre = :id");
$stmt->execute(['id' => $id_livre]);
$livre = $stmt->fetch();

if (!$livre || $livre['nb_disponible'] <= 0) {
    flash_set('error', "Livre indisponible ❌");
    header("Location: ../livres/liste.php");
    exit();
}

// Créer l’emprunt
$date_emprunt = date("Y-m-d");
// Par défaut : 7 jours après la date d’emprunt
$date_retour_prevue = date("Y-m-d", strtotime("+7 days"));

$stmt = $pdo->prepare("INSERT INTO emprunt 
    (id_adherent, id_livre, date_emprunt, date_retour_prevue, statut, penalite) 
    VALUES (:id_adherent, :id_livre, :date_emprunt, :date_retour_prevue, 'en_cours', 0.00)");
$stmt->execute([
    'id_adherent' => $_SESSION['user']['id'],
    'id_livre' => $id_livre,
    'date_emprunt' => $date_emprunt,
    'date_retour_prevue' => $date_retour_prevue
]);

// Décrémenter disponibilité
$stmt = $pdo->prepare("UPDATE livre SET nb_disponible = nb_disponible - 1 WHERE id_livre = :id");
$stmt->execute(['id' => $id_livre]);

flash_set('success', "Livre emprunté avec succès ✅ (retour prévu le $date_retour_prevue)");
header("Location: ../livres/liste.php");
exit();
