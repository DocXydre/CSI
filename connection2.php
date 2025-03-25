<?php
session_start();
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'];
    $motdepasse = $_POST['motdepasse'];

    $stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE mailUtilisateur = ?");
    $stmt->bind_param("s", $identifiant);
    $stmt->execute();
    $result = $stmt->get_result();
    $utilisateur = $result->fetch_assoc();
    $mdp_hash = password_hash($utilisateur['mdpUtilisateur'], PASSWORD_DEFAULT);

    if (password_verify($motdepasse, $mdp_hash)) {
        $_SESSION['user'] = [
            'nom' => $utilisateur['nomUtilisateur'],
            'prenom' => $utilisateur['prenomUtilisateur'],
            'role' => $utilisateur['roleUtilisateur'],
            'mailUtilisateur' => $utilisateur['mailUtilisateur']
        ];

        header("Location: gestion_atelier.php");
        exit;
    } else {
        $_SESSION['error_message'] = "pb de connexion";
        $erreur = "Email ou mot de passe incorrect.";
        echo $erreur;
    }
}
?>