<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $atelierId = $_POST['atelierId'];

    header("Location: gestion_atelier.php");
    exit();
}

$conn->close();
?>
