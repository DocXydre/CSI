<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $atelierId = $_POST['atelierId'];

    $sql = "UPDATE Atelier SET statutAtelier = 'Annulé' WHERE IDAtelier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $atelierId);

    if ($stmt->execute()) {
        header("Location: gestion_atelier.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour du statut de l'atelier : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
