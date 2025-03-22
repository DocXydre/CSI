<?php
$host = 'localhost';
$dbname = 'FERME';
$user = 'root'; // adapte selon ton environnement
$pass = 'root';     // idem pour le mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $identifiant = $_POST['identifiant'];
        $motdepasse = $_POST['motdepasse'];

        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE mailUtilisateur = :id AND mdpUtilisateur = :mdp");
        $stmt->execute([
            ':id' => $identifiant,
            ':mdp' => $motdepasse
        ]);

        if ($stmt->rowCount() == 1) {
            // Connexion réussie -> redirection
            header("Location: dashboard.html");
            exit();
        } else {
            echo "Identifiants incorrects.";
        }
    }
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
