<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stockId = $_POST['stockId'];
    $quantite = $_POST['quantite'];

    $mailUtilisateur = $_SESSION['user']['mailUtilisateur'];
    $dateSuppression = date("Y-m-d H:i:s");

    $sql = "UPDATE StockProduit 
            SET quantiteDisponible = GREATEST(quantiteDisponible - ?, 0), 
                quantiteSortie = ?, 
                historiqueStock = ?, 
                mailUtilisateur = ? 
            WHERE IDStock = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $quantite, $quantite, $dateSuppression, $mailUtilisateur, $stockId);

    if ($stmt->execute()) {
        $result = $conn->query("SELECT nomProduit FROM Produit p JOIN StockProduit s ON s.produitStocke = p.IDProduit WHERE s.IDStock = '$stockId'");
        $row = $result->fetch_assoc();
        $nomProduit = urlencode($row['nomProduit']);
        $date = urlencode($dateSuppression);
        $mail = urlencode($mailUtilisateur);

        header("Location: gestion_stock.php?suppression=$quantite&produit=$nomProduit&date=$date&mail=$mail");
        exit();
    } else {
        echo "Erreur lors de la suppression du stock : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
