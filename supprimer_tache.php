<?php
$host = 'localhost';
$dbname = 'FERME';
$user = 'root';
$pass = 'root';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tacheId = $_POST['tacheId'];

    $sql = "DELETE FROM Tache WHERE IDTache = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tacheId);

    if ($stmt->execute()) {
        header("Location: gestion_woofer.php");
        exit();
    } else {
        echo "Erreur lors de la suppression de la tâche : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
