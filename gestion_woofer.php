<?php
session_start();
include 'config.php';





// Récupérer les woofers
$sql = "SELECT * FROM Utilisateur WHERE roleUtilisateur = 'Woofer'";
$result = $conn->query($sql);
$woofers = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Woofers</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .woofer-item {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .woofer-item h3 {
            margin-top: 0;
        }

        .taches {
            margin-top: 10px;
            background-color: #fff;
            border-left: 4px solid #007BFF;
            padding: 10px;
            border-radius: 6px;
        }

        .taches h4 {
            margin-top: 0;
        }

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
            <img src="src/profil.jpeg" class="profil" alt="Photo de profil">
            <h3><?php echo $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom']; ?></h3>
            <h4><?php echo $_SESSION['user']['role']; ?></h4>
        </div>
        <div>
            <h3>Menu</h3>
            <ul>
                <li class="menu-item"><a href="dashboard.html"><img src="src/icon/dashboard-icon.png" alt="Dashboard"><span>Tableau de bord</span></a></li>
                <li class="menu-item"><a href="gestion_stock.php"><img src="src/icon/stock-icon.png" alt="Stock"><span>Stock</span></a></li>
                <li class="menu-item selected"><a href="gestion_woofer.php"><img src="src/icon/woofer-icon.png" alt="Woofer"><span>Woofer</span></a></li>
                <li class="menu-item"><a href="gestion_atelier.php"><img src="src/icon/atelier-icon.png" alt="Ateliers"><span>Ateliers</span></a></li>
                <li class="menu-item"><a href="vente.php"><img src="src/icon/sales-icon.png" alt="Ventes"><span>Ventes</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <div class="title">Ajouter un Woofer</div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="gestion_woofer.php" method="POST">
                <input type="hidden" name="ajout_woofer" value="1">

                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="dateNaissance">Date de naissance :</label>
                <input type="date" id="dateNaissance" name="dateNaissance" required>

                <label for="dateArrivee">Date d'arrivée :</label>
                <input type="date" id="dateArrivee" name="dateArrivee" required>

                <label for="dateDepart">Date de départ :</label>
                <input type="date" id="dateDepart" name="dateDepart" required>

                <label for="motdepasse">Mot de passe :</label>
                <input type="password" id="motdepasse" name="motdepasse" required>

                <button type="submit">Ajouter</button>
            </form>
        </div>

        <div class="section">
            <div class="title">Liste des Woofers</div>
            <?php foreach ($woofers as $woofer): ?>
                <div class="woofer-item">
                    <h3><?php echo $woofer['prenomUtilisateur'] . ' ' . $woofer['nomUtilisateur']; ?></h3>
                    <p>Âge : <?php echo date_diff(date_create($woofer['dateNaissance']), date_create('today'))->y; ?> ans</p>
                    <p>Date d'arrivée : <?php echo $woofer['dateArrivee']; ?></p>
                    <p>Date de départ : <?php echo $woofer['dateDepart']; ?></p>

                    <div class="action-buttons">
                        <form action="supprimer_woofer.php" method="POST" onsubmit="return confirm('Supprimer ce woofer ?');">
                            <input type="hidden" name="supprimer_woofer" value="1">
                            <input type="hidden" name="mailWoofer" value="<?php echo $woofer['mailUtilisateur']; ?>">
                            <button type="submit" class="delete">Supprimer</button>
                        </form>

                        <button class="edit" onclick="showEditForm('<?php echo $woofer['mailUtilisateur']; ?>')">Modifier dates</button>
                    </div>

                    <div id="edit-form-<?php echo $woofer['mailUtilisateur']; ?>" style="display:none; margin-top: 10px;">
                        <form action="modifier_woofer.php" method="POST">
                            <input type="hidden" name="mailWoofer" value="<?php echo $woofer['mailUtilisateur']; ?>">

                            <label for="dateArrivee-<?php echo $woofer['mailUtilisateur']; ?>">Nouvelle date d'arrivée :</label>
                            <input type="date" id="dateArrivee-<?php echo $woofer['mailUtilisateur']; ?>" name="dateArrivee" required>

                            <label for="dateDepart-<?php echo $woofer['mailUtilisateur']; ?>">Nouvelle date de départ :</label>
                            <input type="date" id="dateDepart-<?php echo $woofer['mailUtilisateur']; ?>" name="dateDepart" required>

                            <button type="submit">Enregistrer</button>
                        </form>
                    </div>

                    <div class="taches">
                        <h4>Tâches assignées :</h4>
                        <?php
                        $sql = "SELECT * FROM Tache WHERE mailUtilisateur = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $woofer['mailUtilisateur']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $taches = $result->fetch_all(MYSQLI_ASSOC);
                        foreach ($taches as $tache):
                        ?>
                            <p><?php echo $tache['nomTache']; ?> - <?php echo $tache['dateTache']; ?></p>
                            <form action="supprimer_tache.php" method="POST" style="display:inline;">
                                <input type="hidden" name="tacheId" value="<?php echo $tache['IDTache']; ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        <?php endforeach; ?>
                        <form action="ajouter_tache.php" method="POST">
                            <input type="hidden" name="mailWoofer" value="<?php echo $woofer['mailUtilisateur']; ?>">
                            <input type="text" name="nomTache" placeholder="Nom de la tâche" required>
                            <input type="datetime-local" name="dateTache" required>
                            <button type="submit">Ajouter une tâche</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function showEditForm(mailWoofer) {
            const form = document.getElementById(`edit-form-${mailWoofer}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>

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
