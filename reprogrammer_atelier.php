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
    $newDate = $_POST['newDate'];
    $dateAtelier = date('Y-m-d', strtotime($newDate));
    $heureDebut = date('H:i:s', strtotime($newDate));
    $heureFin = date('H:i:s', strtotime($newDate . ' + 2 hours')); // Exemple : durée de 2 heures

    $sql = "UPDATE Atelier SET dateAtelier = ?, heureDebut = ?, heureFin = ? WHERE IDAtelier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $dateAtelier, $heureDebut, $heureFin, $atelierId);

    if ($stmt->execute()) {
        header("Location: gestion_atelier.php");
        exit();
    } else {
        echo "Erreur lors de la reprogrammation de l'atelier : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
