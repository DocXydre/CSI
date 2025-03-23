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

// Récupérer les transactions de la base de données
$sql = "SELECT v.*, u.prenomUtilisateur, u.nomUtilisateur FROM Vente v JOIN Utilisateur u ON v.mailUtilisateur = u.mailUtilisateur";
$result = $conn->query($sql);
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .transaction-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .details {
            display: none;
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
                <li class="menu-item">
                    <a href="gestion_atelier.html">
                        <img src="src/icon/atelier-icon.png" alt="Ateliers"> <span>Ateliers</span>
                    </a>
                </li>
                <li class="menu-item selected">
                    <a href="ventes.html">
                        <img src="src/icon/sales-icon.png" alt="Ventes"> <span>Ventes</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <div class="title">Ajouter une Vente</div>
            <form action="ajout_vente.php" method="POST">
                <label for="dateVente">Date de la vente :</label>
                <input type="date" id="dateVente" name="dateVente" required>

                <label for="mailVendeur">Vendeur :</label>
                <select id="mailVendeur" name="mailVendeur" required>
                    <?php
                    $sql = "SELECT mailUtilisateur, prenomUtilisateur, nomUtilisateur FROM Utilisateur WHERE roleUtilisateur = 'Woofer'";
                    $result = $conn->query($sql);
                    $vendeurs = $result->fetch_all(MYSQLI_ASSOC);
                    foreach ($vendeurs as $vendeur):
                    ?>
                        <option value="<?php echo $vendeur['mailUtilisateur']; ?>">
                            <?php echo $vendeur['prenomUtilisateur'] . ' ' . $vendeur['nomUtilisateur']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Ajouter</button>
            </form>
        </div>

        <div class="section">
            <div class="title">Liste des Transactions</div>
            <?php foreach ($transactions as $transaction): ?>
                <div class="transaction-item">
                    <h3>Vente du <?php echo $transaction['dateVente']; ?></h3>
                    <p>Vendeur : <?php echo $transaction['prenomUtilisateur'] . ' ' . $transaction['nomUtilisateur']; ?></p>
                    <p>Prix total : <?php echo $transaction['prixTotal']; ?> €</p>
                    <button onclick="toggleDetails(<?php echo $transaction['IDVente']; ?>)">Plus</button>
                    <div class="details" id="details-<?php echo $transaction['IDVente']; ?>">
                        <?php
                        $sql = "SELECT d.*, p.nomProduit, p.prixUnit FROM DetailsVente d JOIN Produit p ON d.IDStock = p.IDProduit WHERE d.IDVente = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $transaction['IDVente']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $details = $result->fetch_all(MYSQLI_ASSOC);
                        foreach ($details as $detail):
                        ?>
                            <p><?php echo $detail['nomProduit']; ?> - <?php echo $detail['quantiteVendue']; ?> unités - <?php echo $detail['prixUnit']; ?> €</p>
                        <?php endforeach; ?>
                    </div>
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

    <script>
        function toggleDetails(venteId) {
            var details = document.getElementById('details-' + venteId);
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'block';
            } else {
                details.style.display = 'none';
            }
        }
    </script>
</body>
</html>
