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
    $nom = $_POST['nom'];
    $date = $_POST['date'];
    $prix = $_POST['prix'];
    $dateAtelier = date('Y-m-d', strtotime($date));
    $heureDebut = date('H:i:s', strtotime($date));
    $heureFin = date('H:i:s', strtotime($date . ' + 2 hours')); // Exemple : durée de 2 heures

    $sql = "INSERT INTO Atelier (nomAtelier, description, dateAtelier, heureDebut, heureFin, prixAtelier, statutAtelier, participantsMax, mailWoofer, categorieProduit) VALUES (?, ?, ?, ?, ?, ?, 'EnPréparation', 5, 'alanwautot_54@icloud.com', 'Fromages')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssds", $nom, $nom, $dateAtelier, $heureDebut, $heureFin, $prix);

    if ($stmt->execute()) {
        header("Location: gestion_atelier.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'atelier : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
