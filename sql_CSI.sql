CREATE DATABASE IF NOT EXISTS FERME;
USE FERME;

CREATE TABLE IF NOT EXISTS Participant(
    mailParticipant VARCHAR(100) NOT NULL,
    dateNaissance DATE,
    prenomParticipant VARCHAR(100),
    nomParticipant VARCHAR(100),
    PRIMARY KEY (mailParticipant)
);

CREATE TABLE IF NOT EXISTS Utilisateur(
    mailUtilisateur VARCHAR(100) NOT NULL,
    dateNaissance DATE,
    prenomUtilisateur VARCHAR(100),
    nomUtilisateur VARCHAR(100),
    roleUtilisateur ENUM('Woofer', 'Administrateur'),
    mdpUtilisateur VARCHAR(100) NOT NULL,
    dateArrivee DATE,
    dateDepart DATE,
    PRIMARY KEY (mailUtilisateur)
);

CREATE TABLE IF NOT EXISTS Tache(
    IDTache INT NOT NULL AUTO_INCREMENT,
    nomTache VARCHAR(100) NOT NULL,
    description VARCHAR(250),
    dateTache DATE,
    heureDebut TIME,
    heureFin TIME,
    mailUtilisateur VARCHAR(100),
    PRIMARY KEY (IDTache),
    FOREIGN KEY (mailUtilisateur) REFERENCES Utilisateur(mailUtilisateur)
);

CREATE TABLE IF NOT EXISTS Categorie(
    nomCategorie VARCHAR(100) NOT NULL,
    PRIMARY KEY (nomCategorie)
);

CREATE TABLE IF NOT EXISTS Atelier(
    IDAtelier INT NOT NULL AUTO_INCREMENT,
    nomAtelier VARCHAR(100) NOT NULL,
    description VARCHAR(250),
    dateAtelier DATE,
    heureDebut TIME,
    heureFin TIME,
    prixAtelier FLOAT,
    statutAtelier ENUM('EnPréparation', 'Plein', 'EnCours', 'Terminé', 'Annulé'),
    participantsMax INT,
    mailWoofer VARCHAR(100),
    categorieProduit VARCHAR(100),
    PRIMARY KEY (IDAtelier),
    FOREIGN KEY (mailWoofer) REFERENCES Utilisateur(mailUtilisateur),
    FOREIGN KEY (categorieProduit) REFERENCES Categorie(nomCategorie)
);

CREATE TABLE IF NOT EXISTS Participation(
    mailParticipant VARCHAR(100),
    IDAtelier INT,
    dateInscription DATE,
    PRIMARY KEY (mailParticipant, IDAtelier),
    FOREIGN KEY (mailParticipant) REFERENCES Participant(mailParticipant),
    FOREIGN KEY (IDAtelier) REFERENCES Atelier(IDAtelier)
);

CREATE TABLE IF NOT EXISTS Produit(
    IDProduit INT NOT NULL AUTO_INCREMENT,
    nomProduit VARCHAR(100) NOT NULL,
    prixUnit FLOAT,
    categorieProduit VARCHAR(100),
    PRIMARY KEY (IDProduit),
    FOREIGN KEY (categorieProduit) REFERENCES Categorie(nomCategorie)
);

CREATE TABLE IF NOT EXISTS Vente(
    IDVente INT NOT NULL AUTO_INCREMENT,
    dateVente DATE,
    prixTotal FLOAT NOT NULL,
    mailUtilisateur VARCHAR(100),
    PRIMARY KEY (IDVente),
    FOREIGN KEY (mailUtilisateur) REFERENCES Utilisateur(mailUtilisateur)
);

CREATE TABLE IF NOT EXISTS StockProduit(
    IDStock INT NOT NULL AUTO_INCREMENT,
    quantiteDisponible INT,
    quantiteEntree INT,
    quantiteSortie INT,
    historiqueStock DATETIME,
    datePeremption DATE,
    mailUtilisateur VARCHAR(100),
    produitStocke INT,
    PRIMARY KEY (IDStock),
    FOREIGN KEY (mailUtilisateur) REFERENCES Utilisateur(mailUtilisateur),
    FOREIGN KEY (produitStocke) REFERENCES Produit(IDProduit)
);

CREATE TABLE DetailsVente(
    IDVente INT,
    IDStock INT,
    quantiteVendue INT,
    PRIMARY KEY (IDVente, IDStock),
    FOREIGN KEY (IDVente) REFERENCES Vente(IDVente),
    FOREIGN KEY (IDStock) REFERENCES StockProduit(IDStock)
);


-- Procédure pour calculer le prix total de la vente en multipliant le prix unitaire
-- d'un produit par la quantité vendue pour chaque produit dans la vente
CREATE PROCEDURE calculerPrixTotalVente(IN p_IDVente INT)
BEGIN
    DECLARE total FLOAT DEFAULT 0;

    SELECT SUM(p.prixUnit * d.quantiteVendue)
    INTO total
    FROM DetailsVente d
    INNER JOIN StockProduit s ON d.IDStock = s.IDStock
    INNER JOIN Produit p ON s.produitStocke = p.IDProduit
    WHERE d.IDVente = p_IDVente;

    -- Mettre à jour directement le prix total dans la table Vente
    UPDATE Vente
    SET prixTotal = IFNULL(total, 0)
    WHERE IDVente = p_IDVente;
END;

-- Trigger qui s'active après l'insertion de données sur DetailsVente pour calculer
-- le prix total de la vente et le mettre à jour dans Vente
CREATE TRIGGER updatePrixTotalAfterInsert
AFTER INSERT ON DetailsVente
FOR EACH ROW
BEGIN
  CALL calculerPrixTotalVente(NEW.IDVente);
END;

-- Trigger qui s'active après la modification de données sur DetailsVente pour calculer
-- le prix total de la vente et le mettre à jour dans Vente
CREATE TRIGGER updatePrixTotalAfterUpdate
AFTER UPDATE ON DetailsVente
FOR EACH ROW
BEGIN
  CALL calculerPrixTotalVente(NEW.IDVente);
END;

-- Trigger qui s'active après la suppression de données sur DetailsVente pour calculer
-- le prix total de la vente et le mettre à jour dans Vente
CREATE TRIGGER updatePrixTotalAfterDelete
AFTER DELETE ON DetailsVente
FOR EACH ROW
BEGIN
  CALL calculerPrixTotalVente(OLD.IDVente);
END;


-- Procédure pour calculer le nombre de participants dans un atelier
CREATE PROCEDURE calculerNombreParticipantsAtelier(IN p_IDAtelier INT, OUT nb_participants INT)
BEGIN
    SELECT COUNT(*) INTO nb_participants
    FROM Participation
    WHERE IDAtelier = p_IDAtelier;
END;

-- Trigger qui renvoie une erreur si on ajoute un participant alors que
-- le nombre de participants max dans un atelier est atteint
CREATE TRIGGER verifierLimiteParticipants
BEFORE INSERT ON Participation
FOR EACH ROW
BEGIN
    DECLARE nb INT DEFAULT 0;
    DECLARE maxParticipants INT;

    CALL calculerNombreParticipantsAtelier(NEW.IDAtelier, nb);

    SELECT participantsMax INTO maxParticipants
    FROM Atelier
    WHERE IDAtelier = NEW.IDAtelier;

    IF nb >= maxParticipants THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Erreur : nombre maximum de participants atteint pour cet atelier';
    END IF;
END;


-- Trigger qui s'active après une vente pour vérifier si la quantité vendue
-- d'un produit ne dépasse pas celle disponible en stock et qui met à jour
-- la quantité disponible et la quantité de sortie dans le stock d'un produit
CREATE TRIGGER verifierStockUpdateStock
BEFORE INSERT ON DetailsVente
FOR EACH ROW
BEGIN
  DECLARE qteDispo INT;

  SELECT quantiteDisponible INTO qteDispo
  FROM StockProduit WHERE IDStock = NEW.IDStock;

  IF NEW.quantiteVendue > qteDispo THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erreur : quantité vendue supérieure à la quantité disponible en stock';
  END IF;

  -- Mise à jour sortie + disponible
  UPDATE StockProduit
  SET quantiteSortie = quantiteSortie + NEW.quantiteVendue,
      quantiteDisponible = quantiteDisponible - NEW.quantiteVendue
  WHERE IDStock = NEW.IDStock;
END;


-- Trigger qui s'active avant la création d'une tâche pour vérifier si la date
-- de la tâche se passe avant la date de départ du woofer
CREATE TRIGGER verifierDateTacheBeforeInsert
BEFORE INSERT ON Tache
FOR EACH ROW
BEGIN
  DECLARE dDepart DATE;
  SELECT dateDepart INTO dDepart
  FROM Utilisateur
  WHERE mailUtilisateur = NEW.mailUtilisateur;

  IF NEW.dateTache > dDepart THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erreur : la date de la tâche dépasse la date de départ de l utilisateur';
  END IF;
END;

-- Trigger qui s'active avant la modification d'une tâche pour vérifier si la date
-- de la tâche se passe avant la date de départ du woofer
CREATE TRIGGER verifierDateTacheBeforeUpdate
BEFORE UPDATE ON Tache
FOR EACH ROW
BEGIN
  DECLARE dDepart DATE;
  SELECT dateDepart INTO dDepart
  FROM Utilisateur
  WHERE mailUtilisateur = NEW.mailUtilisateur;

  IF NEW.dateTache > dDepart THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erreur : la date de la tâche dépasse la date de départ de l utilisateur';
  END IF;
END;


-- Trigger qui s'active avant la création d'un atelier pour vérifier si la date
-- de l'atelier se passe avant la date de départ du woofer
CREATE TRIGGER verifierDateAtelierBeforeInsert
BEFORE INSERT ON Atelier
FOR EACH ROW
BEGIN
  DECLARE dDepart DATE;
  SELECT dateDepart INTO dDepart
  FROM Utilisateur
  WHERE mailUtilisateur = NEW.mailWoofer;

  IF NEW.dateAtelier > dDepart THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erreur : la date de l atelier dépasse la date de départ du Woofer';
  END IF;
END;

-- Trigger qui s'active avant la modification d'un atelier pour vérifier si la date
-- de l'atelier se passe avant la date de départ du woofer
CREATE TRIGGER verifierDateAtelierBeforeUpdate
BEFORE UPDATE ON Atelier
FOR EACH ROW
BEGIN
  DECLARE dDepart DATE;
  SELECT dateDepart INTO dDepart
  FROM Utilisateur
  WHERE mailUtilisateur = NEW.mailWoofer;

  IF NEW.dateAtelier > dDepart THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erreur : la date de l atelier dépasse la date de départ du Woofer';
  END IF;
END;


-- Trigger qui s'active avant l'inscription d'un participant à un atelier
-- pour vérifier s'il s'inscrit bien avant la date de l'atelier
CREATE TRIGGER verifierDateInscriptionBeforeInsert
BEFORE INSERT ON Participation
FOR EACH ROW
BEGIN
  DECLARE dAtelier DATE;
  SELECT dateAtelier INTO dAtelier
  FROM Atelier
  WHERE IDAtelier = NEW.IDAtelier;

  IF NEW.dateInscription > dAtelier THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erreur : la date d inscription dépasse la date de l atelier';
  END IF;
END;

