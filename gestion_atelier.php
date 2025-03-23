<?php
$host = 'localhost';
$dbname = 'FERME';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les ateliers
$query = "SELECT * FROM Atelier";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ateliers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="side-box left-box">
        <div class="profil">
            <img src="src/profil.jpeg" class="profil" alt="Photo de profil">
            <h3>MATHIS Thomas</h3>
            <h4>admin</h4>
        </div>
        <div>
            <h3>Menu</h3>
            <ul>
                <li class="menu-item">
                    <a href="dashboard.html">
                        <img src="src/icon/dashboard-icon.png" alt="Tableau de bord"> 
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="gestion_stock.html">
                        <img src="src/icon/stock-icon.png" alt="Stock"> <span>Stock</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="gestion_woofer.html">
                        <img src="src/icon/woofer-icon.png" alt="Woofer"> <span>Woofer</span>
                    </a>
                </li>
                <li class="menu-item selected">
                    <a href="gestion_atelier.html">
                        <img src="src/icon/atelier-icon.png" alt="Ateliers"> <span>Ateliers</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ventes.html">
                        <img src="src/icon/sales-icon.png" alt="Ventes"> <span>Ventes</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="section">
            <h2>Ajouter un Atelier</h2>
            <form action="ajout_atelier.php" method="POST">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="date">Date :</label>
                <input type="date" id="date" name="date" required>

                <label for="prix">Prix (€) :</label>
                <input type="number" id="prix" name="prix" required>

                <button type="submit">Ajouter</button>
            </form>
        </div>

        <div class="section">
            <h2>Liste des Ateliers</h2>
            <?php while ($atelier = $result->fetch_assoc()): ?>
                <div class="atelier-block">
                    <h3><?= htmlspecialchars($atelier['nomAtelier']) ?></h3>
                    <p><strong>Date :</strong> <?= $atelier['dateAtelier'] ?></p>
                    <p><strong>Prix :</strong> <?= $atelier['prixAtelier'] ?> €</p>
                    <a href="modifier_atelier.php?id=<?= $atelier['IDAtelier'] ?>">Modifier</a>
                    <a href="supprimer_atelier.php?id=<?= $atelier['IDAtelier'] ?>" onclick="return confirm('Supprimer cet atelier ?');">Supprimer</a>
                    <a href="gerer_participants.php?id=<?= $atelier['IDAtelier'] ?>">Gérer Participants</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div class="side-box right-box">
        <div class="notif">
            <h3>Notifications</h3>
             <div class="notif-item" style="background-color: #FF000060;">
                <img src="src/icon/alerte-icon.png" alt="Alerte" >
                <p>Remplir stock</p>
                <h5>Maintenant</h5>
            </div>
            <div class="notif-item">
                <img src="src/icon/woofer-icon.png" alt="Woofer">
                <p>Nouveau Woofer Quentin</p>
                <h5>Il y a 12 minutes</h5>
            </div>
        </div>
        <div class="sur-place">
            <h3>Woofer en ligne</h3>
            <div class="woofer-item">
                <img src="src/profil.jpeg" class="profil" alt="Photo de profil">
                <p>Thomas</p>
                <h5>Activité Poules</h5>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
