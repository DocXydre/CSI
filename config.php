<?php
$host = "localhost"; // ou l'adresse de ton serveur SQL
$dbname = "nom_de_ta_base"; // Mets le bon nom
$username = "root"; // Ton utilisateur
$password = ""; // Ton mot de passe

// Connexion avec MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
