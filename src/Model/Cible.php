<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Cible extends Model
{
    public function getCiblesByIdMission($idMission)
    // retourne toutes les cibles selon l'id de missions
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
        SELECT
            Cibles.idCible,
            Cibles.isActive,
            Cibles.firstname,
            Cibles.lastname,
            Cibles.birthdate,
            Cibles.codeName,
            Cible.cibleName AS cibleCible
        FROM
            Cibles
        JOIN
            CiblesInMission ON Cibles.idCible = CiblesInMission.idCible
        JOIN
            Missions ON CiblesInMission.idMission = Missions.idMission
        JOIN
            Cible ON Cibles.cibleCible = Cible.idCible
        WHERE
            Missions.idMission = :idMission';

            $stmt = $bdd->prepare($req);

            if (!empty ($idMission)) {
                $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $cibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $cibles;
                }
            }

        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getSearchCibleNames($cibleName, $page, $itemsPerPage)
    // retourne les cibles recherchées
    // si vide toutes les cibles
    {
        try {
            // Calculer l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req =
                "SELECT Cibles.idCible AS id,
            Cibles.codeName AS valeur,
            Cibles.isActive,
            Cibles.firstname,
            Cibles.lastname,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Cibles.countryCible,
            Country.countryName
            FROM Cibles
            JOIN Country ON Cibles.countryCible = Country.idCountry
            WHERE Cibles.codeName LIKE :cibleName
            ORDER BY idCible
            LIMIT :offset, :itemsPerPage";
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($cibleName) and !empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':cibleName', '%' . $cibleName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $cible = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $cible;
                    }
                } else {
                    return $this->getAllCibleNames();
                }
            } else {
                return 'une erreur est survenue';
            }

        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getAllCibleNames()
    // retourne toutes les cibles
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT Cibles.idCible AS id,
            Cibles.codeName AS valeur,
            Cibles.isActive,
            Cibles.firstname,
            Cibles.lastname,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Cibles.countryCible,
            Country.countryName
            FROM Cibles
            JOIN Country ON Cibles.countryCible = Country.idCountry
            ";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $cible = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $cible;
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getByCibleId($cibleId)
    // retourne la cible selon son id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT Cibles.idCible AS id,
            Cibles.codeName AS valeur,
            Cibles.isActive,
            Cibles.firstname,
            Cibles.lastname,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Cibles.countryCible,
            Country.countryName
            FROM Cibles
            JOIN Country ON Cibles.countryCible = Country.idCountry
            WHERE idCible  = :cibleId";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($cibleId)) {
                    $stmt->bindValue(':cibleId', $cibleId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $cible = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $cible;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getRelatedCible($cibleId)
    // Récupère tous les éléments liés à une cible
    {
        try {

            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers Cible
            $tables = array(
                'Contacts' => 'idContact',
                'Agents' => 'idAgent',
                'CiblesInMission' => 'idCible'
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "";

                // Utilisation de switch-case pour gérer les différents cas
                switch ($tableName) {
                    case 'Contacts':
                        $req = "SELECT Cibles.codeName
                        FROM Cibles
                        JOIN Contacts ON Contacts.idContact = Cibles.idCible
                        WHERE Cibles.idCible = :cibleId";
                        break;

                    case 'CiblesInMission':
                        $req = "
                        SELECT Missions.codeName AS missionCodeName
                        FROM CiblesInMission
                        JOIN Missions ON CiblesInMission.idMission = Missions.idMission
                        WHERE CiblesInMission.idCible = :cibleId;";
                        break;

                    default:
                        $req = "SELECT * FROM $tableName WHERE $foreignKey = :cibleId";
                        break;
                }

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty ($cibleId) and !empty ($cibleId)) {
                        $stmt->bindValue(':cibleId', $cibleId, PDO::PARAM_INT);
                        if ($stmt->execute()) {
                            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $stmt->closeCursor();

                            // Ajout des résultats à la liste
                            $relatedElements[$tableName] = $results;
                        } else {
                            return 'une erreur est survenue';
                        }
                    }
                } else {
                    return 'une erreur est survenue';
                }
            }

            // Retourne la liste des éléments liés
            return $relatedElements;

        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getPaginationAllCibleNames($page, $itemsPerPage)
    // retourne toutes les cibles triées par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
        SELECT Cibles.idCible AS id,
        Cibles.codeName AS valeur,
        Cibles.isActive,
        Cibles.firstname,
        Cibles.lastname,
        DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
        Cibles.countryCible,
        Country.countryName
        FROM Cibles
        JOIN Country ON Cibles.countryCible = Country.idCountry
        ORDER BY idCible
        LIMIT :offset, :itemsPerPage";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $cible = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $cible;
                    }
                }

            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function addCible($cibleName, $cibleFirstName, $cibleLastName, $cibleBirthDate, $cibleCountry, $cibleActive)
    {
        // Ajoute une cible
        try {

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Cibles (isActive, firstname, lastname, birthdate, codeName, countryCible)
            VALUES (:isActive, :firstname, :lastname, :birthdate, :codeName, :countryCible)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($cibleName)) {
                    $stmt->bindValue(':isActive', $cibleActive, PDO::PARAM_INT);
                    $stmt->bindValue(':firstname', $cibleFirstName, PDO::PARAM_STR);
                    $stmt->bindValue(':lastname', $cibleLastName, PDO::PARAM_STR);
                    $stmt->bindValue(':birthdate', $cibleBirthDate, PDO::PARAM_STR);
                    $stmt->bindValue(':codeName', $cibleName, PDO::PARAM_STR);
                    $stmt->bindValue(':countryCible', $cibleCountry, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        return 'La cible suivante a bien été ajoutée : ' . $cibleName;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            return 'une erreur est survenue';
        }
    }

    public function deleteCible($cibleId)
    // Supprime la cible selon l'id
    {
        try {


            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Cibles
            WHERE idCible  = :cibleId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($cibleId)) {
                    $stmt->bindValue(':cibleId', $cibleId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La cible a bien été supprimée ';
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            return 'Une erreur est survenue';
        }
    }

    public function updateCible($cibleId, $cibleName, $cibleFirstName, $cibleLastName, $cibleBirthDate, $cibleCountry, $cibleActive)
    {
        try {
            // Modifie la cible selon l'id

            $bdd = $this->connexionPDO();
            $req = "UPDATE Cibles SET";

            // Initialiser un tableau pour stocker les éléments à mettre à jour
            $updateValues = array();

            // Construire la requête dynamiquement en fonction des éléments fournis
            if (!empty ($cibleName)) {
                $req .= " codeName = :codeName,";
                $updateValues[':codeName'] = $cibleName;
            }
            if (!empty ($cibleFirstName)) {
                $req .= " firstname = :firstname,";
                $updateValues[':firstname'] = $cibleFirstName;
            }
            if (!empty ($cibleLastName)) {
                $req .= " lastname = :lastname,";
                $updateValues[':lastname'] = $cibleLastName;
            }
            if (!empty ($cibleBirthDate)) {
                $req .= " birthdate = :birthdate,";
                $updateValues[':birthdate'] = $cibleBirthDate;
            }
            if (!empty ($cibleCountry)) {
                $req .= " countryCible = :countryCible,";
                $updateValues[':countryCible'] = $cibleCountry;
            }
            if (isset ($cibleActive)) {
                $req .= " isActive = :isActive,";
                $updateValues[':isActive'] = $cibleActive;
            }

            // Supprimer la virgule finale de la requête
            $req = rtrim($req, ',');

            // Ajouter la condition WHERE pour spécifier l'ID de la cible à mettre à jour
            $req .= " WHERE idCible = :idCible";
            $updateValues[':idCible'] = $cibleId;

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($cibleId)) {
                    foreach ($updateValues as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    if ($stmt->execute()) {
                        return 'La cible a bien été modifiée : ' . $cibleName;
                    }
                }
            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            return 'Une erreur est survenue';
        }
    }
}
