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

// Vérifier que la réservation existe
$stmt = $pdo->prepare("SELECT r.*, l.nb_disponible 
                       FROM reservation r
                       JOIN livre l ON l.id_livre = r.id_livre
                       WHERE r.id_reservation = :id");
$stmt->execute(['id' => $id_reservation]);
$reservation = $stmt->fetch();

if (!$reservation) {
    flash_set('error', "Réservation introuvable.");
    header("Location: liste.php");
    exit();
}

// Vérifier que le livre est disponible
if ($reservation['nb_disponible'] < 1) {
    flash_set('error', "Le livre n'est pas disponible pour attribution.");
    header("Location: liste.php");
    exit();
}

// Mettre la réservation en attribuée
$stmt = $pdo->prepare("UPDATE reservation 
                       SET statut = 'attribuee' 
                       WHERE id_reservation = :id");
$stmt->execute(['id' => $id_reservation]);

// Créer un nouvel emprunt pour l’adhérent
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

// Décrémenter le nombre disponible du livre
$stmt = $pdo->prepare("UPDATE livre 
                       SET nb_disponible = nb_disponible - 1 
                       WHERE id_livre = :id");
$stmt->execute(['id' => $reservation['id_livre']]);

// Supprimer la réservation pour éviter le blocage "Déjà réservé"
$stmt = $pdo->prepare("DELETE FROM reservation WHERE id_reservation = :id");
$stmt->execute(['id' => $id_reservation]);

flash_set('success', "Réservation attribuée et emprunt créé ✅");
header("Location: liste.php");
exit();
