<?php
$host = 'localhost';
$dbname = 'FERME';
$user = 'root';
$pass = 'root';

// Connexion à la base de données avec mysqli
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mailWoofer = $_POST['mailWoofer'];
    // Ajouter ici la logique pour modifier les informations du woofer

    header("Location: gestion_woofer.php");
    exit();
}

$conn->close();
?>
