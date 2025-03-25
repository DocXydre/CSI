<?php
session_start();
include 'config.php';

<<<<<<< Updated upstream
$sql = "SELECT v.*, u.prenomUtilisateur, u.nomUtilisateur FROM Vente v JOIN Utilisateur u ON v.mailUtilisateur = u.mailUtilisateur";
=======
$message = "";

// Récupérer les produits (avec stock) pour affichage dans le formulaire
$sql = "SELECT s.IDStock, p.nomProduit FROM StockProduit s JOIN Produit p ON s.produitStocke = p.IDProduit";
>>>>>>> Stashed changes
$result = $conn->query($sql);
$stocks = $result->fetch_all(MYSQLI_ASSOC);

// Traitement du formulaire de vente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dateVente'], $_POST['produits'])) {
    $dateVente = $_POST['dateVente'];
    $produitsVendus = json_decode($_POST['produits'], true); // tableau [{id, quantite}]
    $mailUtilisateur = $_SESSION['user']['mailUtilisateur']; // correction ici si clé 'mail' dans $_SESSION

    $stmt = $conn->prepare("INSERT INTO Vente (dateVente, prixTotal, mailUtilisateur) VALUES (?, 0, ?)");
    $stmt->bind_param("ss", $dateVente, $mailUtilisateur);

    if ($stmt->execute()) {
        $idVente = $conn->insert_id;

        $stmtDetail = $conn->prepare("INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue) VALUES (?, ?, ?)");
        foreach ($produitsVendus as $produit) {
            $idStock = $produit['id'];
            $quantite = $produit['quantite'];
            $stmtDetail->bind_param("iii", $idVente, $idStock, $quantite);
            $stmtDetail->execute();
        }

        $message = "✅ Vente enregistrée avec succès !";
    } else {
        $message = "❌ Erreur : " . $stmt->error;
    }
}

// Récupération de l’historique des ventes
$ventes = [];
$sql = "SELECT v.IDVente, v.dateVente, v.prixTotal, p.nomProduit, dv.quantiteVendue
        FROM Vente v
        JOIN DetailsVente dv ON v.IDVente = dv.IDVente
        JOIN StockProduit s ON dv.IDStock = s.IDStock
        JOIN Produit p ON s.produitStocke = p.IDProduit
        ORDER BY v.dateVente DESC";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $idVente = $row['IDVente'];
    if (!isset($ventes[$idVente])) {
        $ventes[$idVente] = [
            'date' => $row['dateVente'],
            'prix' => $row['prixTotal'],
            'produits' => []
        ];
    }
    $ventes[$idVente]['produits'][] = [
        'nom' => $row['nomProduit'],
        'quantite' => $row['quantiteVendue']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Ventes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .action-buttons {
            margin-top: 10px;
        }

        .action-buttons form {
            display: inline-block;
            margin-right: 10px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-buttons .delete {
            background-color: #dc3545;
            color: white;
        }

        .action-buttons .edit {
            background-color: #ffc107;
            color: black;
        }

        .message {
            font-weight: bold;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="side-box left-box">
    <div class="profil">
<<<<<<< Updated upstream
            <img src="src/profil.jpeg" class="profil" alt="Photo de profil">
            <h3><?php echo $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom']; ?></h3>
            <h4><?php echo $_SESSION['user']['role']; ?></h4>
        </div>
        <div>
            <h3>Menu</h3>
            <ul>
                <li class="menu-item">
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
                <li class="menu-item selected">
                    <a href="vente.php">
                        <img src="src/icon/sales-icon.png" alt="Ventes"> <span>Ventes</span>
                    </a>
                </li>
            </ul>
        </div>
=======
        <img src="src/profil.jpeg" class="profil" alt="Photo de profil">
        <h3><?php echo $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom']; ?></h3>
        <h4><?php echo $_SESSION['user']['role']; ?></h4>
    </div>
    <div>
        <h3>Menu</h3>
        <ul>
            <li class="menu-item"><a href="dashboard.html"><img src="src/icon/dashboard-icon.png" alt="Dashboard"><span>Tableau de bord</span></a></li>
            <li class="menu-item"><a href="gestion_stock.php"><img src="src/icon/stock-icon.png" alt="Stock"><span>Stock</span></a></li>
            <li class="menu-item"><a href="gestion_woofer.php"><img src="src/icon/woofer-icon.png" alt="Woofer"><span>Woofer</span></a></li>
            <li class="menu-item"><a href="gestion_atelier.php"><img src="src/icon/atelier-icon.png" alt="Ateliers"><span>Ateliers</span></a></li>
            <li class="menu-item selected"><a href="vente.php"><img src="src/icon/sales-icon.png" alt="Ventes"><span>Ventes</span></a></li>
        </ul>
    </div>
</div>

<!-- Contenu principal -->
<div class="container">
    <div class="vente-form">
        <h2>Nouvelle Vente</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return prepareProduitsAvantEnvoi();">
            <label for="dateVente">Date de la vente :</label>
            <input type="datetime-local" name="dateVente" id="dateVente" required>

            <div id="produits-container">
                <div class="ligne-produit">
                    <select>
                        <?php foreach ($stocks as $stock): ?>
                            <option value="<?php echo $stock['IDStock']; ?>"><?php echo $stock['nomProduit']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" placeholder="Quantité" min="1" value="1">
                </div>
            </div>

            <button type="button" class="add-btn" onclick="ajouterLigneProduit()">+ Ajouter un produit</button>
            <input type="hidden" name="produits" id="produits-json">
            <button type="submit">Enregistrer la vente</button>
        </form>
>>>>>>> Stashed changes
    </div>

    <div class="ventes-liste">
        <h2>Historique des Ventes</h2>

        <?php if (empty($ventes)): ?>
            <p>Aucune vente enregistrée.</p>
        <?php else: ?>
            <?php foreach ($ventes as $vente): ?>
                <div class="vente-block">
                    <div class="vente-date">🗓️ <?php echo date("d/m/Y H:i", strtotime($vente['date'])); ?></div>
                    <?php foreach ($vente['produits'] as $p): ?>
                        <div class="produit-ligne">- <?php echo $p['quantite'] . " x " . $p['nom']; ?></div>
                    <?php endforeach; ?>
                    <div class="vente-prix">💰 Total : <?php echo number_format($vente['prix'], 2); ?> €</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
function ajouterLigneProduit() {
    const container = document.getElementById('produits-container');
    const ligne = container.firstElementChild.cloneNode(true);
    ligne.querySelector('input').value = 1;
    container.appendChild(ligne);
}

function prepareProduitsAvantEnvoi() {
    const lignes = document.querySelectorAll('.ligne-produit');
    const produits = [];

    lignes.forEach(ligne => {
        const select = ligne.querySelector('select');
        const input = ligne.querySelector('input');
        produits.push({
            id: parseInt(select.value),
            quantite: parseInt(input.value)
        });
    });

    document.getElementById('produits-json').value = JSON.stringify(produits);
    return true;
}
</script>

</body>
</html>
