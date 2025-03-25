<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mailWoofer = $_POST['mailWoofer'];
    $dateArrivee = $_POST['dateArrivee'];
    $dateDepart = $_POST['dateDepart'];

    // Update the dates in the database
    $sql = "UPDATE Utilisateur SET dateArrivee = ?, dateDepart = ? WHERE mailUtilisateur = ? AND roleUtilisateur = 'Woofer'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $dateArrivee, $dateDepart, $mailWoofer);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Dates mises à jour avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la mise à jour : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the Woofer management page
    header("Location: gestion_woofer.php");
    exit();
}
?>
