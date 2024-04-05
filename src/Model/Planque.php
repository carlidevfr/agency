<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Planque extends Model
{
    public function getPlanquesByIdMission($idMission)
    // retourne toutes les planques de la mission
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
        SELECT
            Planques.idPlanque,
            Planques.planqueName,
            Planques.location,
            Planques.type,
            Country.countryName AS planqueCountry
        FROM
            Planques
        JOIN
            Missions ON Planques.actuallyMission = Missions.idMission
        JOIN
            Country ON Planques.planqueCountry = Country.idCountry
        WHERE
            Missions.idMission = :idMission';

            $stmt = $bdd->prepare($req);

            if (!empty($idMission)) {
                $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $planques = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $planques;
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

    public function getAllPlanqueNames()
    // retourne toutes les planques
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
            SELECT idPlanque AS id, planqueName AS valeur, Planques.location, Planques.type, Country.countryName AS planqueCountry, Country.idCountry AS planqueCountryId, Missions.title AS missionName
            FROM Planques
            JOIN Country ON planqueCountry = Country.idCountry
            LEFT JOIN Missions ON Planques.actuallyMission = Missions.idMission';
            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $planque = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $planque;
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

    public function getSearchPlanqueNames($PlanqueName, $page, $itemsPerPage)
    // retourne les planques recherchées
    // si vide toutes les planques
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
            SELECT idPlanque AS id, planqueName AS valeur, Planques.location, Planques.type, Country.countryName AS planqueCountry, Missions.title AS missionName
            FROM Planques
            JOIN Country ON planqueCountry = Country.idCountry
            LEFT JOIN Missions ON Planques.actuallyMission = Missions.idMission
            WHERE planqueName LIKE :planqueName
            ORDER BY idPlanque
            LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($PlanqueName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':planqueName', '%' . $PlanqueName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $planque = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $planque;
                    }
                } else {
                    return $this->getAllPlanqueNames();
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

    public function getPaginationAllPlanqueNames($page, $itemsPerPage)
    {
        try {
            // retourne tous les planque triés par page

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idPlanque AS id, planqueName AS valeur, Planques.location, Planques.type, Country.countryName AS planqueCountry, Missions.title AS missionName
        FROM Planques
        JOIN Country ON planqueCountry = Country.idCountry
        LEFT JOIN Missions ON Planques.actuallyMission = Missions.idMission
        ORDER BY idPlanque
        LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $planque = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $planque;
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

    public function getByPlanqueId($PlanqueId)
    {
        try {
            // retourne la planque en fonction de son id

            $bdd = $this->connexionPDO();
            $req = '
            SELECT Planques.idPlanque AS id, Planques.planqueName AS valeur, Planques.location, Planques.type, Country.countryName AS planqueCountry
            FROM Planques
            JOIN Country ON planqueCountry = Country.idCountry
            WHERE idPlanque  = :PlanqueId';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($PlanqueId)) {
                    $stmt->bindValue(':PlanqueId', $PlanqueId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $planque = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $planque;
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

    public function addPlanque($planqueName, $planqueAdress, $planqueCountry, $planqueMission, $planqueType)
    {
        try {
            // Ajoute une planque

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Planques (planqueName, location, planqueCountry, actuallyMission, type)
            VALUES (:planqueName, :location, :planqueCountry, :actuallyMission, :type)';
            
            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($planqueName)) {
                    $stmt->bindValue(':planqueName', $planqueName, PDO::PARAM_STR);
                    $stmt->bindValue(':location', $planqueAdress, PDO::PARAM_STR);
                    $stmt->bindValue(':planqueCountry', $planqueCountry, PDO::PARAM_INT);
                    $stmt->bindValue(':actuallyMission', $planqueMission, PDO::PARAM_INT);
                    $stmt->bindValue(':type', $planqueType, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        return 'La planque suivant a bien été ajoutée : ' . $planqueName;
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

    public function deletePlanque($planqueId)
    // Supprime la planque selon l'id
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Planques
            WHERE idPlanque  = :planqueId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($planqueId)) {
                    $stmt->bindValue(':planqueId', $planqueId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La planque a bien été supprimée ';
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

    public function updatePlanque($planqueId, $newName, $newAdress, $newCountry, $newMission, $newType)
    {
        try {
            // Modifie la planque selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            UPDATE Planques
            SET planqueName = :newPlanqueName,
            location = :updatedAddress,
            planqueCountry = :updatedCountry,
            type = :updatedType,
            actuallyMission = :updatedMission
            WHERE idPlanque  = :PlanqueId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($planqueId) and !empty($newName)) {
                    $stmt->bindValue(':PlanqueId', $planqueId, PDO::PARAM_INT);
                    $stmt->bindValue(':newPlanqueName', $newName, PDO::PARAM_STR);
                    $stmt->bindValue(':updatedAddress', $newAdress, PDO::PARAM_STR);
                    $stmt->bindValue(':updatedCountry', $newCountry, PDO::PARAM_INT);
                    $stmt->bindValue(':updatedType', $newType, PDO::PARAM_STR);
                    $stmt->bindValue(':updatedMission', $newMission, PDO::PARAM_INT);

                    
                    if ($stmt->execute()) {
                        return 'La planque a bien été modifiée : ' . $newName;
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
