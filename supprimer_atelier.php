<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $atelierId = $_POST['atelierId'];

    $sql = "DELETE FROM Atelier WHERE IDAtelier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $atelierId);

    if ($stmt->execute()) {
        header("Location: gestion_atelier.php");
        exit();
    } else {
        echo "Erreur lors de la suppression de l'atelier : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
