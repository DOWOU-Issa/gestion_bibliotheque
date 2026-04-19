<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestion Bibliothèque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-4">
<?php
// Afficher les messages flash si disponibles
if (function_exists('flash_get')) {
    if ($s = flash_get('success')) echo '<div class="alert alert-success">'.e($s).'</div>';
    if ($e = flash_get('error'))   echo '<div class="alert alert-danger">'.e($e).'</div>';
}
?>
