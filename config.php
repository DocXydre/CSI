<?php
$host = "localhost"; 
$dbname = "FERME"; 
$username = "root";
$password = "root"; 

// Connexion avec MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
