<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Speciality extends Model
{
    public function getAllSpecialityNames()
    // retourne toutes les specialités de missions
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
        SELECT idSpeciality AS id, speName AS valeur
        FROM Speciality';
            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $speciality = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $speciality;
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

    public function getSearchSpecialityNames($speName, $page, $itemsPerPage)
    // retourne les specialités recherchées
    // si vide retourne tout
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idSpeciality AS id, speName AS valeur
        FROM Speciality
        WHERE speName LIKE :specialityName
        ORDER BY idSpeciality
        LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($speName) and !empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':specialityName', '%' . $speName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $speciality = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $speciality;
                    }
                } else {
                    return $this->getAllSpecialityNames();
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

    public function getPaginationAllSpecialityNames($page, $itemsPerPage)
    // retourne toutes les specialités triées par page
    {
        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idSpeciality AS id, speName AS valeur
        FROM Speciality
        ORDER BY idSpeciality
        LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $speciality = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $speciality;
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

    public function getBySpecialityId($SpecialityId)
    // retourne la specialité en fonction de son id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            SELECT idSpeciality AS id, speName AS valeur
            FROM Speciality
        WHERE idSpeciality  = :SpecialityId';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($SpecialityId)) {
                    $stmt->bindValue(':SpecialityId', $SpecialityId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $speciality = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $speciality;
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

    public function getRelatedSpeciality($specialityId)
    // Récupère tous les éléments liés à une specialité
    {
        try {

            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers speciality
            $tables = array(
                'Missions' => 'missionSpeciality',
                'AgentsSpecialities' => 'speciality_id',
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {
                $req = "";
                $bdd = $this->connexionPDO();

                // Utilisation de switch-case pour gérer les différents cas
                switch ($tableName) {
                    case 'AgentsSpecialities':
                        $req = "SELECT Agents.idAgent, Agents.codeAgent
                        FROM Agents
                        INNER JOIN AgentsSpecialities ON Agents.idAgent = AgentsSpecialities.agent_id
                        WHERE AgentsSpecialities.speciality_id = :specialityId";
                        break;

                    default:
                        $req = "SELECT * FROM $tableName WHERE $foreignKey = :specialityId";
                        break;
                }

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty ($specialityId) and !empty ($specialityId)) {
                        $stmt->bindValue(':specialityId', $specialityId, PDO::PARAM_INT);
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

    public function addSpeciality($specialityName)
    // Ajoute une specialité
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Speciality (speName)
            VALUES (:specialityName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($specialityName)) {
                    $stmt->bindValue(':specialityName', $specialityName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La speciality suivant a bien été ajoutée : ' . $specialityName;
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

    public function deleteSpeciality($specialityId)
    // Supprime la specialité selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Speciality
            WHERE idSpeciality  = :specialityId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($specialityId)) {
                    $stmt->bindValue(':specialityId', $specialityId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La speciality a bien été supprimée ';
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

    public function updateSpeciality($SpecialityId, $newName)
    // Modifie la specialité selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            UPDATE Speciality
            SET speName = :newspeName
            WHERE idSpeciality  = :SpecialityId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($SpecialityId) and !empty ($newName)) {
                    $stmt->bindValue(':SpecialityId', $SpecialityId, PDO::PARAM_INT);
                    $stmt->bindValue(':newspeName', $newName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'La speciality a bien été modifiée : ' . $newName;
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