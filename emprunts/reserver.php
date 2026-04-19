<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('adherent');

$id_livre = intval($_GET['id'] ?? 0);
if ($id_livre < 1) {
    flash_set('error', "Livre invalide.");
    header("Location: ../livres/liste.php");
    exit();
}

// Vérifier disponibilité
$stmt = $pdo->prepare("SELECT nb_disponible FROM livre WHERE id_livre = :id");
$stmt->execute(['id' => $id_livre]);
$livre = $stmt->fetch();

if (!$livre) {
    flash_set('error', "Livre introuvable.");
    header("Location: ../livres/liste.php");
    exit();
}

if ($livre['nb_disponible'] > 0) {
    flash_set('error', "Ce livre est disponible, empruntez-le directement.");
    header("Location: ../livres/liste.php");
    exit();
}

// Vérifier si déjà réservé ou emprunté
$stmt = $pdo->prepare("SELECT id_reservation 
                       FROM reservation 
                       WHERE id_livre = :id_livre 
                         AND id_adherent = :id_adherent 
                         AND statut IN ('en_attente','attribuee','notifiee')");
$stmt->execute([
    'id_livre' => $id_livre,
    'id_adherent' => $_SESSION['user']['id']
]);

if ($stmt->fetch()) {
    flash_set('error', "Vous avez déjà une réservation pour ce livre.");
    header("Location: mes_reservations.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id_emprunt 
                       FROM emprunt 
                       WHERE id_livre = :id_livre 
                         AND id_adherent = :id_adherent 
                         AND statut IN ('en_cours','retard')");
$stmt->execute([
    'id_livre' => $id_livre,
    'id_adherent' => $_SESSION['user']['id']
]);

if ($stmt->fetch()) {
    flash_set('error', "Vous avez déjà emprunté ce livre.");
    header("Location: ../livres/liste.php");
    exit();
}

// Créer la réservation (sans toucher à nb_disponible)
$stmt = $pdo->prepare("INSERT INTO reservation (id_livre, id_adherent, date_reservation, statut) 
                       VALUES (:id_livre, :id_adherent, NOW(), 'en_attente')");
$stmt->execute([
    'id_livre' => $id_livre,
    'id_adherent' => $_SESSION['user']['id']
]);

flash_set('success', "Réservation enregistrée ✅");
header("Location: mes_reservations.php");
exit();
