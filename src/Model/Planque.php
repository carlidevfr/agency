<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Planque extends Model
{
    public function getPlanquesByIdMission($idMission)
    {
        try {
            // retourne toutes les planques de la mission

            $bdd = $this->connexionPDO();
            $req = '
        SELECT
            Planques.idPlanque,
            Planques.planqueName,
            Planques.location,
            Planques.planque,
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
    {
        try {
            // retourne tous les planque de missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idPlanque AS id, planqueName AS valeur
        FROM Planques';
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
    {
        try {

            // retourne les planque recherchés
            // si vide tous les planque

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idPlanque AS id, planqueName AS valeur
        FROM Planques
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
        SELECT idPlanque AS id, planqueName AS valeur
        FROM Planques
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
            SELECT idPlanque AS id, planqueName AS valeur
            FROM Planques
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

    public function getRelatedPlanque($planqueId)
    // Récupère tous les éléments liés à un planque
    {
        try {

            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers planque
            $tables = array(
                'Missions' => 'missionPlanque'
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "SELECT * FROM $tableName WHERE $foreignKey = :planqueId";

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty ($planqueId) and !empty ($planqueId)) {
                        $stmt->bindValue(':planqueId', $planqueId, PDO::PARAM_INT);
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

    public function addPlanque($planqueName)
    {
        try {
            // Ajoute un planque

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Planques (planqueName)
            VALUES (:planqueName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($planqueName)) {
                    $stmt->bindValue(':planqueName', $planqueName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le planque suivant a bien été ajouté : ' . $planqueName;
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
    {
        try {
            // Supprime la planque selon l'id

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

    public function updatePlanque($PlanqueId, $newName)
    {
        try {
            // Modifie la planque selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            UPDATE Planques
            SET PlanqueName = :newPlanqueName
            WHERE idPlanque  = :PlanqueId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($PlanqueId) and !empty($newName)) {
                    $stmt->bindValue(':PlanqueId', $PlanqueId, PDO::PARAM_INT);
                    $stmt->bindValue(':newPlanqueName', $newName, PDO::PARAM_STR);
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
