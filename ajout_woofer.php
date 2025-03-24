<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['dateNaissance'];
    $dateArrivee = $_POST['dateArrivee'];
    $dateDepart = $_POST['dateDepart'];
    $motdepasse = $_POST['motdepasse'];

    // Génération d'un email fictif
    $mail = strtolower($prenom . $nom . '@example.com');

    // Préparer la requête SQL
    $sql = "INSERT INTO Utilisateur (mailUtilisateur, dateNaissance, prenomUtilisateur, nomUtilisateur, roleUtilisateur, mdpUtilisateur, dateArrivee, dateDepart) 
            VALUES (?, ?, ?, ?, 'Woofer', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $mail, $dateNaissance, $prenom, $nom, $motdepasse, $dateArrivee, $dateDepart);

    if ($stmt->execute()) {
        $message = "Woofer ajouté avec succès !";
    } else {
        $message = "Erreur lors de l'ajout : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Woofer</title>
    <link rel="stylesheet" href="style.css">
    <style>
        form {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        input, label {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            color: green;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Ajouter un nouveau Woofer</h2>

<form method="POST" action="">
    <label for="prenom">Prénom :</label>
    <input type="text" name="prenom" id="prenom" required>

    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>

    <label for="dateNaissance">Date de naissance :</label>
    <input type="date" name="dateNaissance" id="dateNaissance" required>

    <label for="dateArrivee">Date d'arrivée :</label>
    <input type="date" name="dateArrivee" id="dateArrivee" required>

    <label for="dateDepart">Date de départ :</label>
    <input type="date" name="dateDepart" id="dateDepart" required>

    <label for="motdepasse">Mot de passe :</label>
    <input type="password" name="motdepasse" id="motdepasse" required>

    <input type="submit" value="Ajouter le Woofer">
</form>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

</body>
</html>
