<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$id_reservation = intval($_GET['id'] ?? 0);
if ($id_reservation < 1) {
    flash_set('error', "Réservation invalide.");
    header("Location: liste.php");
    exit();
}

// Vérifier que la réservation existe et est en attente
$stmt = $pdo->prepare("SELECT * FROM reservation WHERE id_reservation = :id");
$stmt->execute(['id' => $id_reservation]);
$reservation = $stmt->fetch();

if (!$reservation) {
    flash_set('error', "Réservation introuvable.");
    header("Location: liste.php");
    exit();
}

if ($reservation['statut'] !== 'en_attente') {
    flash_set('error', "Cette réservation n'est pas en attente.");
    header("Location: liste.php");
    exit();
}

// Mettre à jour le statut en 'notifiee'
$stmt = $pdo->prepare("UPDATE reservation SET statut = 'notifiee' WHERE id_reservation = :id");
$stmt->execute(['id' => $id_reservation]);

flash_set('success', "Réservation notifiée à l'adhérent ✅");
header("Location: liste.php");
exit();
