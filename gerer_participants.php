<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $atelierId = $_POST['atelierId'];
    // Ajouter ici la logique pour gérer les participants (ajouter, supprimer, etc.)

    header("Location: gestion_atelier.php");
    exit();
}

$conn->close();
?>
