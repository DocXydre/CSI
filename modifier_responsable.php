<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $atelierId = $_POST['atelierId'];
    $nouveau_responsable = $_POST['nouveau_responsable'];

    if (!empty($atelier_id) && !empty($nouveau_responsable) && filter_var($nouveau_responsable, FILTER_VALIDATE_EMAIL)) {
        $sql = "UPDATE ateliers SET mailWoofer = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nouveau_responsable, $atelier_id);

        if ($stmt->execute()) {
            echo "Responsable modifié avec succès.";
        } else {
            echo "Erreur lors de la modification : " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Veuillez fournir un ID d'atelier valide et une adresse email valide.";
    }
}

$conn->close();
?>
