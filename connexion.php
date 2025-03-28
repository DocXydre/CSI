<?php
session_start();
include 'config.php'; 

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
            width: 90%;
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
        <form action="test.php" method="POST">
            <input type="text" name="identifiant" placeholder="Identifiant" required>
            <input type="password" name="motdepasse" placeholder="Mot de passe" required>
            <button type="submit">Valider</button>
        </form>
    </div>
</body>
</html>

