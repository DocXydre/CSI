<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stockId = $_POST['stockId'];
    $quantite = $_POST['quantite'];

    // Récupérer l'email de l'utilisateur connecté
    $mailUtilisateur = $_SESSION['user']['mailUtilisateur'];
    $dateAjout = date("Y-m-d H:i:s");

    // Mise à jour du stock (avec mailUtilisateur et date)
    $sql = "UPDATE StockProduit 
            SET quantiteDisponible = quantiteDisponible + ?, 
                quantiteEntree = quantiteEntree + ?, 
                historiqueStock = ?, 
                mailUtilisateur = ? 
            WHERE IDStock = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $quantite, $quantite, $dateAjout, $mailUtilisateur, $stockId);

    if ($stmt->execute()) {
        // Récupération du nom du produit pour afficher dans le message
        $result = $conn->query("SELECT nomProduit FROM Produit p JOIN StockProduit s ON s.produitStocke = p.IDProduit WHERE s.IDStock = '$stockId'");
        $row = $result->fetch_assoc();
        $nomProduit = urlencode($row['nomProduit']);
        $date = urlencode($dateAjout);
        $mail = urlencode($mailUtilisateur);

        header("Location: gestion_stock.php?ajout=$quantite&produit=$nomProduit&date=$date&mail=$mail");
        exit();
    } else {
        echo "Erreur lors de l'ajout du stock : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

?>
