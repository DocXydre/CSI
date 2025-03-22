<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $date = $_POST["date"];
    $prix = $_POST["prix"];

    // Préparer la requête SQL
    $sql = "INSERT INTO ateliers (nom, date, prix) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $nom, $date, $prix); // "ssd" = string, string, double (float)

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Atelier ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout : " . $stmt->error;
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();
}
?>
