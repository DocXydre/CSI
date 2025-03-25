<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['supprimer_woofer'])) {
    if (!empty($_POST['mailWoofer']) && filter_var($_POST['mailWoofer'], FILTER_VALIDATE_EMAIL)) {
        $mail = $_POST['mailWoofer'];

        
        $stmt_atelier = $conn->prepare("DELETE FROM atelier WHERE mailWoofer = ?");
        $stmt_atelier->bind_param("s", $mail);
        $stmt_atelier->execute();
        $stmt_atelier->close();

        $stmt = $conn->prepare("DELETE FROM Utilisateur WHERE mailUtilisateur = ? AND roleUtilisateur = 'Woofer'");
        $stmt->bind_param("s", $mail);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Woofer supprimé avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression : " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Adresse e-mail invalide.";
    }
    header("Location: gestion_woofers.php"); // Redirigez vers une page appropriée
    exit();
}