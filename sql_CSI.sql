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
      quantiteDisponible = quantiteDisponible - NEW.quantiteVendue,
      historiqueStock = NOW()
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

INSERT INTO Utilisateur
VALUES ('alanwautot_54@icloud.com', '2002-12-24', 'Alan', 'Wautot', 'Administrateur', 'mdpAdmin', '2024-01-20', '2025-12-23');
INSERT INTO Utilisateur
VALUES ('thomasmathis@gmail.com', '2003-01-30', 'Thomas', 'Mathis', 'Woofer', 'mdpWooferThomas', '2024-06-25', '2025-08-27');
INSERT INTO Utilisateur
VALUES ('justinburr@outlook.com', '2003-10-17', 'Justin', 'Burr', 'Woofer', 'mdpWooferJustin', '2025-01-10', '2025-12-03');

SELECT * FROM Utilisateur;

INSERT INTO Tache (nomTache, description, dateTache, heureDebut, heureFin, mailUtilisateur)
VALUES ('Nettoyer poulailler', 'Nettoyage du poulailler en retirant les saletés et en récupérant les oeufs', '2025-03-01', '15:00:00', '17:00:00', 'justinburr@outlook.com');
INSERT INTO Tache (nomTache, description, dateTache, heureDebut, heureFin, mailUtilisateur)
VALUES ('Traire vaches', 'Traire les vaches pour récupérer le lait', '2025-04-06', '14:00:00', '16:00:00', 'thomasmathis@gmail.com');

SELECT * FROM Tache;

INSERT INTO Participant VALUES ('elodiepoirier@gmail.com', '2004-07-20', 'Elodie', 'Poirier');
INSERT INTO Participant VALUES ('elianarnaud@free.fr', NULL, 'Elian', 'Arnaud');

SELECT * FROM Participant;

INSERT INTO Categorie VALUES ('OEufs');
INSERT INTO Categorie VALUES ('Laits');
INSERT INTO Categorie VALUES ('Legumes');
INSERT INTO Categorie VALUES ('Fromages');

SELECT * FROM Categorie;

INSERT INTO Atelier (nomAtelier, description, dateAtelier, heureDebut, heureFin, prixAtelier, statutAtelier, participantsMax, mailWoofer, categorieProduit)
VALUES ('Fabrication de fromages', 'Venez fabriquer vos propres fromages avec Justin !', '2025-03-30', '14:00:00', '17:00:00', 25.00, 'EnPréparation', 5, 'justinburr@outlook.com', 'Fromages');
INSERT INTO Atelier (nomAtelier, description, dateAtelier, heureDebut, heureFin, prixAtelier, statutAtelier, participantsMax, mailWoofer, categorieProduit)
VALUES ('Culture biologique', 'Venez découvrir la culture biologique et ses bienfaits avec Thomas !', '2025-05-04', '10:30:00', '11:30:00', 12.00, 'EnPréparation', 3, 'thomasmathis@gmail.com', 'Legumes');

SELECT * FROM Atelier;

INSERT INTO Participation VALUES ('elodiepoirier@gmail.com', 1, '2025-03-15');
INSERT INTO Participation VALUES ('elianarnaud@free.fr', 1, '2025-03-12');
INSERT INTO Participation VALUES ('elianarnaud@free.fr', 2, '2025-05-01');

SELECT * FROM Participation;

INSERT INTO Produit (nomProduit, prixUnit, categorieProduit)
VALUES ('Oeufs de poule (boîte de 6)', 3.50, 'OEufs');
INSERT INTO Produit (nomProduit, prixUnit, categorieProduit)
VALUES ('Lait de vache (1L)', 1.80, 'Laits');
INSERT INTO Produit (nomProduit, prixUnit, categorieProduit)
VALUES ('Lait de chèvre (1L)', 1.80, 'Laits');
INSERT INTO Produit (nomProduit, prixUnit, categorieProduit)
VALUES ('Fromage de chèvre', 4.20, 'Fromages');
INSERT INTO Produit (nomProduit, prixUnit, categorieProduit)
VALUES ('Carottes (1kg)', 2.00, 'Legumes');
INSERT INTO Produit (nomProduit, prixUnit, categorieProduit)
VALUES ('Salade', 1.20, 'Legumes');

SELECT * FROM Produit;

INSERT INTO StockProduit (quantiteDisponible, quantiteEntree, quantiteSortie, historiqueStock, datePeremption, mailUtilisateur, produitStocke)
VALUES (10, 10, 0, NOW(), '2025-04-15', 'alanwautot_54@icloud.com', 1);
INSERT INTO StockProduit (quantiteDisponible, quantiteEntree, quantiteSortie, historiqueStock, datePeremption, mailUtilisateur, produitStocke)
VALUES (8, 8, 0, NOW(), '2025-03-30', 'thomasmathis@gmail.com', 2);
INSERT INTO StockProduit (quantiteDisponible, quantiteEntree, quantiteSortie, historiqueStock, datePeremption, mailUtilisateur, produitStocke)
VALUES (6, 6, 0, NOW(), '2025-03-28', 'justinburr@outlook.com', 3);
INSERT INTO StockProduit (quantiteDisponible, quantiteEntree, quantiteSortie, historiqueStock, datePeremption, mailUtilisateur, produitStocke)
VALUES (5, 5, 0, NOW(), '2025-04-10', 'justinburr@outlook.com', 4);
INSERT INTO StockProduit (quantiteDisponible, quantiteEntree, quantiteSortie, historiqueStock, datePeremption, mailUtilisateur, produitStocke)
VALUES (12, 12, 0, NOW(), '2025-04-20', 'thomasmathis@gmail.com', 5);
INSERT INTO StockProduit (quantiteDisponible, quantiteEntree, quantiteSortie, historiqueStock, datePeremption, mailUtilisateur, produitStocke)
VALUES (9, 9, 0, NOW(), '2025-03-29', 'alanwautot_54@icloud.com', 6);

ALTER TABLE StockProduit AUTO_INCREMENT = 1;
SELECT * FROM StockProduit;

INSERT INTO Vente (dateVente, prixTotal, mailUtilisateur)
VALUES ('2025-03-23', 0, 'alanwautot_54@icloud.com');
INSERT INTO Vente (dateVente, prixTotal, mailUtilisateur)
VALUES ('2025-03-24', 0, 'thomasmathis@gmail.com');
INSERT INTO Vente (dateVente, prixTotal, mailUtilisateur)
VALUES ('2025-03-25', 0, 'justinburr@outlook.com');

SELECT * FROM Vente;

INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue)
VALUES (1, 1, 2);
INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue)
VALUES (1, 6, 1);
INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue)
VALUES (2, 2, 2);
INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue)
VALUES (2, 5, 3);
INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue)
VALUES (3, 3, 1);
INSERT INTO DetailsVente (IDVente, IDStock, quantiteVendue)
VALUES (3, 4, 2);

SELECT * FROM DetailsVente;






-- Trigger pour le prixTotal correct
-- Trigger pour le nb max de participants correct (rajouter fontionnalité qui change le statut à 'Plein')
-- Trigger pour la quantité vendue > quantité dispo correct
-- Tous les triggers pour les dates
-- Trigger pour mettre à jour les stocks après une vente correct
-- Ajouter un trigger pour la date de péremption
