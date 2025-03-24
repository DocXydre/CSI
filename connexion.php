<?php
session_start();
require_once 'config.php'; // Fichier qui contient la connexion à la BDD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $mot_de_passe = $_POST['motdepasse'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $identifiant);
    $stmt->execute();
    $result = $stmt->get_result();
    $utilisateur = $result->fetch_assoc();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['user'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'email' => $utilisateur['email']
        ];

        header("Location: gestion_atelier.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!-- Formulaire de connexion simple -->
<form method="post">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required />
    <button type="submit">Connexion</button>
    <?php if (isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>
</form>
