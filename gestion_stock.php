<?php
session_start();
$host = 'localhost';
$dbname = 'FERME';
$user = 'root';
$pass = 'root';

// Connexion à la base de données avec mysqli
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Récupérer les informations de l'utilisateur connecté
$mailUtilisateur = $_SESSION['mailUtilisateur'];
$sql = "SELECT * FROM Utilisateur WHERE mailUtilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $mailUtilisateur);
$stmt->execute();
$result = $stmt->get_result();
$utilisateur = $result->fetch_assoc();

// Récupérer les stocks de la base de données
$sql = "SELECT s.*, p.nomProduit FROM StockProduit s JOIN Produit p ON s.produitStocke = p.IDProduit";
$result = $conn->query($sql);
$stocks = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stock-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .stock-item.out-of-stock {
            background-color: #ffcccc;
        }
    </style>
</head>
<body>
    <div class="side-box left-box">
        <div class="profil">
            <img src="src/profil.jpeg" class="profil" alt="Photo de profil">
            <h3><?php echo $utilisateur['prenomUtilisateur'] . ' ' . $utilisateur['nomUtilisateur']; ?></h3>
            <h4><?php echo $utilisateur['roleUtilisateur']; ?></h4>
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
                <li class="menu-item selected">
                    <a href="gestion_stock.html">
                        <img src="src/icon/stock-icon.png" alt="Stock"> <span>Stock</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="gestion_woofer.html">
                        <img src="src/icon/woofer-icon.png" alt="Woofer"> <span>Woofer</span>
                    </a>
                </li>
                <li class="menu-item">
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
            <div class="title">Gestion des Stocks</div>
            <?php foreach ($stocks as $stock): ?>
                <div class="stock-item <?php echo $stock['quantiteDisponible'] == 0 ? 'out-of-stock' : ''; ?>">
                    <h3><?php echo $stock['nomProduit']; ?></h3>
                    <p>Quantité actuelle : <?php echo $stock['quantiteDisponible']; ?></p>
                    <p>Dernier ajout : <?php echo $stock['quantiteEntree']; ?> unités le <?php echo $stock['historiqueStock']; ?> par <?php echo $stock['mailUtilisateur']; ?></p>
                    <p>Dernière sortie : <?php echo $stock['quantiteSortie']; ?> unités le <?php echo $stock['historiqueStock']; ?> par <?php echo $stock['mailUtilisateur']; ?></p>
                    <form action="ajouter_stock.php" method="POST" style="display:inline;">
                        <input type="hidden" name="stockId" value="<?php echo $stock['IDStock']; ?>">
                        <input type="number" name="quantite" placeholder="Quantité à ajouter" required>
                        <button type="submit">Ajouter</button>
                    </form>
                    <form action="supprimer_stock.php" method="POST" style="display:inline;">
                        <input type="hidden" name="stockId" value="<?php echo $stock['IDStock']; ?>">
                        <input type="number" name="quantite" placeholder="Quantité à supprimer" required>
                        <button type="submit">Supprimer</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="side-box right-box">
        <div class="notif">
            <h3>Notifications</h3>
            <div class="notif-item" style="background-color: #FF000060;">
                <img src="src/icon/alerte-icon.png" alt="Alerte">
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
