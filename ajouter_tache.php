<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mailWoofer = $_POST['mailWoofer'];
    $nomTache = $_POST['nomTache'];
    $dateTache = $_POST['dateTache'];
    $date = date('Y-m-d', strtotime($dateTache));
    $heureDebut = date('H:i:s', strtotime($dateTache));
    $heureFin = date('H:i:s', strtotime($dateTache . ' + 1 hour'));

    $sql = "INSERT INTO Tache (nomTache, description, dateTache, heureDebut, heureFin, mailUtilisateur) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nomTache, $nomTache, $date, $heureDebut, $heureFin, $mailWoofer);

    if ($stmt->execute()) {
        header("Location: gestion_woofer.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de la tâche : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
