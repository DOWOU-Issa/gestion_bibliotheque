<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire', 'admin');

$id_emprunt = intval($_GET['id'] ?? 0);
if ($id_emprunt < 1) {
    flash_set('error', 'Aucun emprunt sélectionné.');
    header("Location: gestion_emprunts.php");
    exit();
}

$date_retour = date("Y-m-d");

// Récupérer l’emprunt
$stmt = $pdo->prepare("SELECT * FROM emprunt WHERE id_emprunt = :id");
$stmt->execute(['id' => $id_emprunt]);
$emprunt = $stmt->fetch();

if (!$emprunt) {
    flash_set('error', 'Emprunt introuvable ❌');
    header("Location: gestion_emprunts.php");
    exit();
}

// Vérifier si déjà rendu
if (!empty($emprunt['date_retour_effective'])) {
    flash_set('error', 'Retour déjà enregistré pour cet emprunt.');
    header("Location: gestion_emprunts.php");
    exit();
}

// Calculer la pénalité
$penalite = 0.00;
$statut = 'rendu';

if (!empty($emprunt['date_retour_prevue']) && $date_retour > $emprunt['date_retour_prevue']) {
    $jours_retard = (strtotime($date_retour) - strtotime($emprunt['date_retour_prevue'])) / (60 * 60 * 24);
    $jours_retard = floor($jours_retard);
    $penalite = $jours_retard * 100; // Exemple : 100 FCFA par jour
    $statut = 'retard';
}

// Mettre à jour l’emprunt
$stmt = $pdo->prepare("UPDATE emprunt 
                       SET date_retour_effective = :date_retour, statut = :statut, penalite = :penalite 
                       WHERE id_emprunt = :id");
$stmt->execute([
    'date_retour' => $date_retour,
    'statut' => $statut,
    'penalite' => $penalite,
    'id' => $id_emprunt,
]);

// Vérifier s’il existe une réservation en attente pour ce livre
$stmt = $pdo->prepare("SELECT * FROM reservation 
                       WHERE id_livre = :id_livre AND statut = 'en_attente' 
                       ORDER BY date_reservation ASC LIMIT 1");
$stmt->execute(['id_livre' => $emprunt['id_livre']]);
$reservation = $stmt->fetch();

if ($reservation) {
    // Attribuer la réservation
    $stmt = $pdo->prepare("UPDATE reservation SET statut = 'attribuee' WHERE id_reservation = :id");
    $stmt->execute(['id' => $reservation['id_reservation']]);

    // Créer un nouvel emprunt pour l’adhérent réservé
    $date_emprunt = date("Y-m-d");
    $date_retour_prevue = date("Y-m-d", strtotime("+7 days"));
    $stmt = $pdo->prepare("INSERT INTO emprunt (id_adherent, id_livre, date_emprunt, date_retour_prevue, statut, penalite) 
                           VALUES (:id_adherent, :id_livre, :date_emprunt, :date_retour_prevue, 'en_cours', 0.00)");
    $stmt->execute([
        'id_adherent' => $reservation['id_adherent'],
        'id_livre' => $emprunt['id_livre'],
        'date_emprunt' => $date_emprunt,
        'date_retour_prevue' => $date_retour_prevue
    ]);
} else {
    // Aucun réservataire → livre dispo
    $stmt = $pdo->prepare("UPDATE livre SET nb_disponible = nb_disponible + 1 WHERE id_livre = :id");
    $stmt->execute(['id' => $emprunt['id_livre']]);
}

flash_set('success', 'Retour enregistré avec succès ✅');
header("Location: gestion_emprunts.php");
exit();
