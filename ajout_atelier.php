<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $date = $_POST['date'];
    $prix = $_POST['prix'];
    $places = $_POST['places'];
    $description = $_POST['description'];
    $responsable = $_POST['responsable'];
    $dateAtelier = date('Y-m-d', strtotime($date));
    $heureDebut = date('H:i:s', strtotime($date));
    $heureFin = date('H:i:s', strtotime($date . ' + 2 hours')); // Exemple : durée de 2 heures

    $sql = "INSERT INTO Atelier (nomAtelier, description, dateAtelier, heureDebut, heureFin, prixAtelier, statutAtelier, participantsMax, mailWoofer, categorieProduit) VALUES (?, ?, ?, ?, ?, ?, 'EnPréparation', ?, ?, 'Fromages')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdsi", $nom, $description, $dateAtelier, $heureDebut, $heureFin, $prix, $places, $responsable);

    if ($stmt->execute()) {
        header("Location: gestion_atelier.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'atelier : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
