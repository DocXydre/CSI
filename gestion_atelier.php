<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

$nom = $_SESSION['user']['nom'];
$prenom = $_SESSION['user']['prenom'];

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

// Récupérer les ateliers de la base de données
$sql = "SELECT * FROM Atelier";
$result = $conn->query($sql);
$ateliers = $result->fetch_all(MYSQLI_ASSOC);
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
            <div class="title">Ajouter un Atelier</div>
            <form action="ajout_atelier.php" method="POST">
                <label for="nom">Nom de l'atelier :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="date">Date :</label>
                <input type="datetime-local" id="date" name="date" required>

                <label for="prix">Prix (€) :</label>
                <input type="number" id="prix" name="prix" required>

                <button type="submit">Ajouter</button>
            </form>
        </div>

        <div class="section">
            <div class="title">Liste des Ateliers</div>
            <?php foreach ($ateliers as $atelier): ?>
                <div class="atelier-item">
                    <h3><?php echo $atelier['nomAtelier']; ?></h3>
                    <p>Date : <?php echo $atelier['dateAtelier']; ?></p>
                    <p>Prix : <?php echo $atelier['prixAtelier']; ?> €</p>
                    <button onclick="openModal(<?php echo $atelier['IDAtelier']; ?>)">Gérer</button>
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

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <script>
        function openModal(atelierId) {
            document.getElementById('modal-body').innerHTML = `
                <h3>Gérer l'atelier</h3>
                <form action="reprogrammer_atelier.php" method="POST">
                    <input type="hidden" name="atelierId" value="${atelierId}">
                    <label for="newDate">Nouvelle date :</label>
                    <input type="datetime-local" id="newDate" name="newDate" required>
                    <button type="submit">Reprogrammer</button>
                </form>
                <form action="gerer_participants.php" method="POST">
                    <input type="hidden" name="atelierId" value="${atelierId}">
                    <button type="submit">Gérer les participants</button>
                </form>
                <form action="supprimer_atelier.php" method="POST">
                    <input type="hidden" name="atelierId" value="${atelierId}">
                    <button type="submit">Supprimer</button>
                </form>
            `;
            document.getElementById('modal').style.display = 'block';
        }

        document.getElementsByClassName('close')[0].onclick = function() {
            document.getElementById('modal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none';
            }
        }
    </script>
</body>
</html>
