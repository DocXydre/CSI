<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dateVente = $_POST['dateVente'];
    $mailVendeur = $_POST['mailVendeur'];

    $sql = "INSERT INTO Vente (dateVente, prixTotal, mailUtilisateur) VALUES (?, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $dateVente, $mailVendeur);

    if ($stmt->execute()) {
        header("Location: ventes.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de la vente : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
