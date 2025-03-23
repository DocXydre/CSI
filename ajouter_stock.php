<?php
$host = 'localhost';
$dbname = 'FERME';
$user = 'root';
$pass = 'root';

// Connexion à la base de données avec mysqli
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stockId = $_POST['stockId'];
    $quantite = $_POST['quantite'];

    $sql = "UPDATE StockProduit SET quantiteDisponible = quantiteDisponible + ?, quantiteEntree = quantiteEntree + ?, historiqueStock = NOW() WHERE IDStock = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $quantite, $quantite, $stockId);

    if ($stmt->execute()) {
        header("Location: gestion_stock.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout du stock : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
