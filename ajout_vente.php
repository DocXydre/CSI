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
