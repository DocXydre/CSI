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
    $atelierId = $_POST['atelierId'];
    // Ajouter ici la logique pour gérer les participants (ajouter, supprimer, etc.)

    header("Location: gestion_atelier.php");
    exit();
}

$conn->close();
?>
