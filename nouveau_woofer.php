// Traitement de l'ajout
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajout_woofer'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['dateNaissance'];
    $dateArrivee = $_POST['dateArrivee'];
    $dateDepart = $_POST['dateDepart'];
    $motdepasse = $_POST['motdepasse'];

    $mail = strtolower($prenom . $nom . '@example.com');

    $sql = "INSERT INTO Utilisateur (mailUtilisateur, dateNaissance, prenomUtilisateur, nomUtilisateur, roleUtilisateur, mdpUtilisateur, dateArrivee, dateDepart) 
            VALUES (?, ?, ?, ?, 'Woofer', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $mail, $dateNaissance, $prenom, $nom, $motdepasse, $dateArrivee, $dateDepart);

    if ($stmt->execute()) {
        $message = "Woofer ajouté avec succès !";
        header("Location: gestion_woofer.php?message=" . urlencode($message));
        exit();
    } else {
        $message = "Erreur lors de l'ajout : " . $stmt->error;
    }

    $stmt->close();
}
