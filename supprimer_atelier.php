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
