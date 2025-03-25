<?php
session_start();
include 'config.php';

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
            <h3><?php echo $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom']; ?></h3>
            <h4><?php echo $_SESSION['user']['role']; ?></h4>
        </div>

        <div>
            <h3>Menu</h3>
            <ul>
                <li class="menu-item selected">
                    <a href="gestion_stock.php">
                        <img src="src/icon/stock-icon.png" alt="Stock"> <span>Stock</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="gestion_woofer.php">
                        <img src="src/icon/woofer-icon.png" alt="Woofer"> <span>Woofer</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="gestion_atelier.php">
                        <img src="src/icon/atelier-icon.png" alt="Ateliers"> <span>Ateliers</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="vente.php">
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
                    <p>
                        Dernier ajout :
                        <?php
                        if (
                            isset($_GET['ajout'], $_GET['produit'], $_GET['date'], $_GET['mail']) &&
                            $_GET['produit'] == $stock['nomProduit']
                        ) {
                            $q = htmlspecialchars($_GET['ajout']);
                            $d = htmlspecialchars($_GET['date']);
                            $m = htmlspecialchars($_GET['mail']);
                            echo "$q " . ($q == 1 ? "unité" : "unités") . " le $d par $m";
                        } else {
                            echo '-';
                        }
                        ?>
                    </p>

                    <p>
                        Dernière sortie :
                        <?php
                        if (
                            isset($_GET['suppression'], $_GET['produit'], $_GET['date'], $_GET['mail']) &&
                            $_GET['produit'] == $stock['nomProduit']
                        ) {
                            $q = htmlspecialchars($_GET['suppression']);
                            $d = htmlspecialchars($_GET['date']);
                            $m = htmlspecialchars($_GET['mail']);
                            echo "$q " . ($q == 1 ? "unité" : "unités") . " le $d par $m";
                        } else {
                            echo '-';
                        }
                        ?>
                    </p>

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
