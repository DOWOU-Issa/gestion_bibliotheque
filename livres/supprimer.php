<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('bibliothecaire','admin');

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM livre WHERE id_livre = :id");
$stmt->execute(['id' => $id]);

flash_set('success','Livre supprimé avec succès.');
header('Location: liste.php');
exit;
