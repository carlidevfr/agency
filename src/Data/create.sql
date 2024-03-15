-- MariaDB
-- on supprime les versions précédentes des tables
DROP DATABASE IF EXISTS secret_agency;
CREATE DATABASE secret_agency;
USE secret_agency;

-- on crée la table spécialités

CREATE TABLE Speciality
(
    idSpeciality INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    speName VARCHAR(250) NOT NULL
) engine=INNODB DEFAULT CHARSET=utf8;

-- on crée la table pays

CREATE TABLE Country
(
    idCountry INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    countryName VARCHAR(250) NOT NULL
) engine=INNODB DEFAULT CHARSET=utf8;

-- on crée la table cible

CREATE TABLE Cibles
(
    idCible INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    isActive TINYINT NOT NULL,
    firstname VARCHAR(250) NOT NULL,
    lastname VARCHAR(250) NOT NULL,
    birthdate DATE NOT NULL,
    codeName VARCHAR(250) NOT NULL,
    countryCible INT NOT NULL,
    FOREIGN KEY (countryCible) REFERENCES Country(idCountry)
) engine=INNODB DEFAULT CHARSET=utf8;

-- on crée la table contacts

CREATE TABLE Contacts
(
    idContact INT NOT NULL PRIMARY KEY,
    FOREIGN KEY (idContact) REFERENCES Cibles(idCible) ON DELETE CASCADE
) engine=INNODB DEFAULT CHARSET=utf8;

-- on crée la table agents

CREATE TABLE Agents
(
    idAgent INT NOT NULL PRIMARY KEY,
    codeAgent VARCHAR(250) NOT NULL,
    FOREIGN KEY (idAgent) REFERENCES Cibles(idCible) ON DELETE CASCADE
) engine=INNODB DEFAULT CHARSET=utf8;

-- table associative des agents et leurs specialités

CREATE TABLE AgentsSpecialities
(
    agent_id INT NOT NULL,
    speciality_id INT NOT NULL,
    PRIMARY KEY (agent_id, speciality_id),
    FOREIGN KEY (agent_id) REFERENCES Agents(idAgent),
    FOREIGN KEY (speciality_id) REFERENCES Speciality(idSpeciality)
) engine=INNODB DEFAULT CHARSET=utf8;

-- Création de la table type de mission

CREATE TABLE Types
(
    idType INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    typeName VARCHAR(250) NOT NULL
) engine=INNODB DEFAULT CHARSET=utf8;

-- Création de la table statut de mission

CREATE TABLE Status
(
    idStatus INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    statusName VARCHAR(250) NOT NULL
) engine=INNODB DEFAULT CHARSET=utf8;

-- Création de la table mission
CREATE TABLE Missions
(
    idMission INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(250) NOT NULL,
    codeName VARCHAR(250) NOT NULL,
    description VARCHAR(250) NOT NULL,
    beginDate DATE NOT NULL,
    endDate DATE NOT NULL,
    missionCountry INT NOT NULL,
    missionType INT NOT NULL,
    missionStatus INT NOT NULL,
    missionSpeciality INT NOT NULL,
    FOREIGN KEY (missionCountry) REFERENCES Country(idCountry),
    FOREIGN KEY (missionType) REFERENCES Types(idType),
    FOREIGN KEY (missionStatus) REFERENCES Status(idStatus),
    FOREIGN KEY (missionSpeciality) REFERENCES Speciality(idSpeciality)
) engine=INNODB DEFAULT CHARSET=utf8;

-- Création de la table planque

CREATE TABLE Planques
(
    idPlanque INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    planqueName VARCHAR(250) NOT NULL,
    location VARCHAR(250) NOT NULL,
    planqueCountry INT NOT NULL,
    actuallyMission INT,
    type VARCHAR(250) NOT NULL,
    FOREIGN KEY (planqueCountry) REFERENCES Country(idCountry),
    FOREIGN KEY (actuallyMission) REFERENCES Missions(idMission)
) engine=INNODB DEFAULT CHARSET=utf8;

-- Table associative lien entre table contact et table mission
CREATE TABLE ContactsInMission
(
    idContact INT NOT NULL,
    idMission INT NOT NULL,
    PRIMARY KEY (idContact, idMission),
    FOREIGN KEY (idContact) REFERENCES Contacts(idContact),
    FOREIGN KEY (idMission) REFERENCES Missions(idMission)
) engine=INNODB DEFAULT CHARSET=utf8;

-- Table associative lien entre table cible et table mission
CREATE TABLE CiblesInMission
(
    idCible INT NOT NULL,
    idMission INT NOT NULL,
    PRIMARY KEY (idCible, idMission),
    FOREIGN KEY (idCible) REFERENCES Cibles(idCible),
    FOREIGN KEY (idMission) REFERENCES Missions(idMission)
) engine=INNODB DEFAULT CHARSET=utf8;

-- Table associative lien entre table agents et table mission
CREATE TABLE AgentsInMission
(
    idAgent INT NOT NULL,
    idMission INT NOT NULL,
    PRIMARY KEY (idAgent, idMission),
    FOREIGN KEY (idAgent) REFERENCES Agents(idAgent),
    FOREIGN KEY (idMission) REFERENCES Missions(idMission)
) engine=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE Admins
(
    idAdmin CHAR(36) NOT NULL PRIMARY KEY, -- UUID
    firstname VARCHAR(250),
    lastname VARCHAR(250),
    email VARCHAR(250) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL
) engine=INNODB DEFAULT CHARSET=utf8;

-- insertion données fictives;

INSERT INTO speciality (speName) VALUES
    ('couteau'),
    ('snipeur'),
    ('discretion');

INSERT INTO country (countryName) VALUES
    ('France'),
    ('Inde'),
    ('Ecosse'),
    ('Pérou');

INSERT INTO Types (typeName) VALUES
    ('surveillance'),
    ('assassinat'),
    ('infiltration');

INSERT INTO Status (statusName) VALUES
    ('en préparation'),
    ('en cours'),
    ('terminé'),
    ('échec');

INSERT INTO Cibles (firstname, lastname, birthdate, codeName, countryCible, isActive) VALUES
    ('Jean', 'Foureau', '1993-01-31', 'cible 1', 1, 1),
    ('Mariette', 'hola', '1995-02-25', 'cible 2', 1, 1 ),
    ('Michèle', 'hajo', '1992-02-25', 'cible 3', 2, 1 ),
    ('René', 'azerty', '1990-02-20', 'cible 4', 3, 1),
    ('José', 'azerty', '1988-02-20', 'cible 5', 1, 1),
    ('Hom', 'ham', '1988-02-20', 'agent1', 3, 0),
    ('bouboule', 'bibule', '1985-02-20', 'agent2', 2,0 ),
    ('parle', 'peu', '1999-02-20', 'contact1', 2,0 ),
    ('parlait', 'peu', '1999-02-20', 'contact2', 3,0 ),
    ('noel', 'santa', '1998-02-20', 'contact3', 1,0 ),
    ('Anthnoy', 'santa', '1998-02-20', 'contact4', 4,0 );


INSERT INTO contacts (idContact) VALUES
    (6),
    (8),
    (9),
    (10),
    (11);

INSERT INTO agents (idAgent, codeAgent) VALUES
    (6, "l'éffaceur"),
    (7, 'simplet');

INSERT INTO agentsspecialities(agent_id, speciality_id) VALUES
    (6,1),
    (6,2),
    (7,3);

INSERT INTO Missions (title, codeName, description, beginDate, endDate, missionCountry, missionType, missionStatus, missionSpeciality) VALUES
    ('Un plan presque parfait <script> alert("hello") </script>','KLURX','Une mission de suivi dans un cardre chalereux','2023-11-28', '2023-12-31', 1, 1, 2, 3),
    ('Un tout petit trou','RODOX','Une mission pour petits et grands','2023-11-25', '2023-12-31', 1, 2, 2, 1),
    ('Un voyage agréable','INDA','Une mission de surveillance','2023-11-25', '2026-12-31', 2, 1, 2, 3),
    ('Un petit souvenir','OUPS','On a besoin de son sac','2023-11-25', '2024-12-31', 4, 3, 2, 3);

INSERT INTO ContactsInMission(idContact, idMission) VALUES
    (10, 1),
    (6, 1),
    (10, 2),
    (8, 3),
    (11, 4);

INSERT INTO CiblesInMission (idCible, idMission) VALUES
    (1, 1),
    (2, 1),
    (2, 2),
    (3, 3),
    (4, 4);


INSERT INTO AgentsInMission (idAgent, idMission) VALUES
    (7, 1),
    (6, 1),
    (6, 2),
    (7, 3),
    (7, 4);

INSERT INTO Planques (planqueName, location, planqueCountry, type, actuallyMission) VALUES
    ('tour eiffel', 'centre de la place', 1,'maison', 1),
    ('Maisonnette', '2 quai des prés 75000 paris', 1, 'maison',1),
    ('le gros immeuble', '2 quai des prés C187 Ville', 2,'maison',NULL),
    ('hutte', '3 bd 2 F487 Ville', 3,'maison',NULL),
    ('hohoha', 'place principale F4787 Ville', 4,'maison',NULL);

INSERT INTO Admins (idAdmin, firstname, lastname, email, password) VALUES
    (UUID(), 'Anthony', 'DOTTOR', 'test@test.fr', '$2y$10$w/Z3seCK31OxqV22WKRY2u51LdlRf9cJ6tFAyOnMUGMJ57I5sxdEm')
;
-- création d'un utilisateur qui servira à faire la connexion dans le .ENV du PHP pour toute l'app;
CREATE OR REPLACE USER 'agenceapp'@'%' IDENTIFIED BY PASSWORD '*54958E764CE10E50764C2EECBB71D01F08549980';
GRANT SELECT, INSERT, UPDATE, DELETE ON secret_agency.* TO 'agenceapp'@'%' ;
