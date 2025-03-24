=<?php
session_start();
require_once 'config.php'; // Fichier qui contient la connexion à la BDD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $mot_de_passe = $_POST['motdepasse'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE mailUtilisateur = ?");
    $stmt->bind_param("s", $identifiant);
    $stmt->execute();
    $result = $stmt->get_result();
    $utilisateur = $result->fetch_assoc();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mdpUtilisateur'])) {
        $_SESSION['user'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nomUtilisateur'],
            'prenom' => $utilisateur['prenomUtilisateur'],
            'email' => $utilisateur['mailUtilisateur']
        ];

        header("Location: gestion_atelier.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            box-sizing: border-box;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .box-connexion {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .box-connexion h2 {
            margin-bottom: 20px;
        }
        .box-connexion input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .box-connexion button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .box-connexion button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="box-connexion">
        <h2>Connexion</h2>
        <form action="connexion.php" method="POST">
            <input type="text" name="identifiant" placeholder="Identifiant" required>
            <input type="password" name="motdepasse" placeholder="Mot de passe" required>
            <button type="submit">Valider</button>
        </form>
    </div>
</body>
</html>

