<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('adherent');

$id_reservation = intval($_GET['id'] ?? 0);
if ($id_reservation < 1) {
    flash_set('error', "Réservation invalide.");
    header("Location: mes_reservations.php");
    exit();
}

// Vérifier que la réservation appartient à l’adhérent connecté
$stmt = $pdo->prepare("SELECT * FROM reservation WHERE id_reservation = :id AND id_adherent = :id_adherent");
$stmt->execute([
    'id' => $id_reservation,
    'id_adherent' => $_SESSION['user']['id']
]);
$reservation = $stmt->fetch();

if (!$reservation) {
    flash_set('error', "Réservation introuvable.");
    header("Location: mes_reservations.php");
    exit();
}

if ($reservation['statut'] !== 'en_attente') {
    flash_set('error', "Seules les réservations en attente peuvent être annulées.");
    header("Location: mes_reservations.php");
    exit();
}

// Annuler la réservation
$stmt = $pdo->prepare("UPDATE reservation SET statut = 'annulee' WHERE id_reservation = :id");
$stmt->execute(['id' => $id_reservation]);

flash_set('success', "Réservation annulée ✅");
header("Location: mes_reservations.php");
exit();