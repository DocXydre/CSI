<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stockId = $_POST['stockId'];
    $quantite = $_POST['quantite'];

    $sql = "UPDATE StockProduit SET quantiteDisponible = quantiteDisponible + ?, quantiteEntree = quantiteEntree + ?, historiqueStock = NOW() WHERE IDStock = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $quantite, $quantite, $stockId);

    if ($stmt->execute()) {
        // Récupérer le nom du produit pour le message
        $result = $conn->query("SELECT nomProduit FROM Produit p JOIN StockProduit s ON s.produitStocke = p.IDProduit WHERE s.IDStock = '$stockId'");
        $row = $result->fetch_assoc();
        $nomProduit = urlencode($row['nomProduit']);
    
        header("Location: gestion_stock.php?ajout=$quantite&produit=$nomProduit");
        exit();
    } else {
        echo "Erreur lors de l'ajout du stock : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
