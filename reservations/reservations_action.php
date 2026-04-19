<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id < 1 || !$action) {
    flash_set('error', "Action invalide.");
    header("Location: reservations.php");
    exit();
}

// Vérifier la réservation
$stmt = $pdo->prepare("SELECT * FROM reservation WHERE id_reservation = :id");
$stmt->execute(['id' => $id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    flash_set('error', "Réservation introuvable.");
    header("Location: reservations.php");
    exit();
}

switch ($action) {
    case 'annuler':
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'annulee' WHERE id_reservation = :id");
        $stmt->execute(['id' => $id]);
        flash_set('success', "Réservation annulée ✅");
        break;

    case 'notifier':
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'notifiee' WHERE id_reservation = :id");
        $stmt->execute(['id' => $id]);
        flash_set('success', "Adhérent notifié 📩");
        break;

    case 'attribuer':
        // Attribuer la réservation et créer un emprunt
        $stmt = $pdo->prepare("UPDATE reservation SET statut = 'attribuee' WHERE id_reservation = :id");
        $stmt->execute(['id' => $id]);

        $date_emprunt = date("Y-m-d");
        $date_retour_prevue = date("Y-m-d", strtotime("+7 days"));
        $stmt = $pdo->prepare("INSERT INTO emprunt (id_adherent, id_livre, date_emprunt, date_retour_prevue, statut, penalite) 
                               VALUES (:id_adherent, :id_livre, :date_emprunt, :date_retour_prevue, 'en_cours', 0.00)");
        $stmt->execute([
            'id_adherent' => $reservation['id_adherent'],
            'id_livre' => $reservation['id_livre'],
            'date_emprunt' => $date_emprunt,
            'date_retour_prevue' => $date_retour_prevue
        ]);

        flash_set('success', "Réservation attribuée et emprunt créé ✅");
        break;

    default:
        flash_set('error', "Action inconnue.");
}

header("Location: reservations.php");
exit();