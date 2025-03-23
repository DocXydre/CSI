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
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['dateNaissance'];
    $dateArrivee = $_POST['dateArrivee'];
    $dateDepart = $_POST['dateDepart'];
    $motdepasse = $_POST['motdepasse'];
    $mail = strtolower($prenom . $nom . '@example.com'); // Exemple de génération de mail

    $sql = "INSERT INTO Utilisateur (mailUtilisateur, dateNaissance, prenomUtilisateur, nomUtilisateur, roleUtilisateur, mdpUtilisateur, dateArrivee, dateDepart) VALUES (?, ?, ?, ?, 'Woofer', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $mail, $dateNaissance, $prenom, $nom, $motdepasse, $dateArrivee, $dateDepart);

    if ($stmt->execute()) {
        header("Location: gestion_woofer.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout du woofer : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
