<?php
$host = 'localhost';
$dbname = 'FERME';
$user = 'root';
$pass = 'root';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mailWoofer = $_POST['mailWoofer'];

    header("Location: gestion_woofer.php");
    exit();
}

$conn->close();
?>
