<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Agent extends Model
{
    public function getAllAgentNames()
    // retourne tous les agents au format json avec les clés 'id' et 'valeur'
    {
        try {

            $bdd = $this->connexionPDO();
            $req = '
                SELECT idAgent AS id, codeAgent AS valeur
                FROM Agents';

            $stmt = $bdd->prepare($req);

            if ($stmt->execute()) {
                $Agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $Agents;
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

    public function getAgentsByIdMission($idMission)
    // retourne tous les agents en mission
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
        SELECT
            Agents.idAgent,
            Agents.codeAgent,
            GROUP_CONCAT(Speciality.speName) AS specialities
        FROM
            Agents
        JOIN
            AgentsInMission ON Agents.idAgent = AgentsInMission.idAgent
        JOIN
            Missions ON AgentsInMission.idMission = Missions.idMission
        JOIN
            AgentsSpecialities ON Agents.idAgent = AgentsSpecialities.agent_id
        JOIN
            Speciality ON AgentsSpecialities.speciality_id = Speciality.idSpeciality
        WHERE
            Missions.idMission = :idMission
        GROUP BY
            Agents.idAgent, Agents.codeAgent';

            $stmt = $bdd->prepare($req);

            if (!empty($idMission)) {
                $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $agents;
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

    public function getNotAgentNames()
    // retourne toutes les personnes qui ne sont pas des agents
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT Cibles.idCible AS id,
            Cibles.codeName AS valeur
            FROM Cibles
            WHERE idCible NOT IN (SELECT idAgent FROM Agents)";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $agent = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $agent;
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

    public function getSearchAgentNames($AgentName, $page, $itemsPerPage)
    // retourne les agents recherchés
    // si vide tous les agents
    {

        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
        SELECT
            Agents.idAgent AS id,
            Agents.codeAgent AS valeur,
            Cibles.firstname,
            Cibles.lastname,
            Cibles.codeName,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Country.countryName AS countryName,
            GROUP_CONCAT(Speciality.speName SEPARATOR ', ') AS specialties
        FROM
            Agents
        JOIN
            Cibles ON Agents.idAgent = Cibles.idCible
        JOIN
            Country ON Cibles.countryCible = Country.idCountry
        LEFT JOIN
            AgentsSpecialities ON Agents.idAgent = AgentsSpecialities.agent_id
        LEFT JOIN
            Speciality ON AgentsSpecialities.speciality_id = Speciality.idSpeciality
        WHERE Agents.codeAgent LIKE :agentName
        GROUP BY
            Agents.idAgent
        ORDER BY idAgent
        LIMIT :offset, :itemsPerPage";
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($AgentName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':agentName', '%' . $AgentName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $agent = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $agent;
                    }
                } else {
                    return $this->getAllAgentNames();
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

    public function getPaginationAllAgentNames($page, $itemsPerPage)
    // retourne tous les agent triés par page
    {
        try {


            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT
            Agents.idAgent AS id,
            Agents.codeAgent AS valeur,
            Cibles.firstname,
            Cibles.lastname,
            Cibles.codeName,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Country.countryName AS countryName,
            GROUP_CONCAT(Speciality.speName SEPARATOR ', ') AS specialties
        FROM
            Agents
        JOIN
            Cibles ON Agents.idAgent = Cibles.idCible
        JOIN
            Country ON Cibles.countryCible = Country.idCountry
        LEFT JOIN
            AgentsSpecialities ON Agents.idAgent = AgentsSpecialities.agent_id
        LEFT JOIN
            Speciality ON AgentsSpecialities.speciality_id = Speciality.idSpeciality
        GROUP BY
            Agents.idAgent
        ORDER BY idAgent
        LIMIT :offset, :itemsPerPage";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $agent = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $agent;
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

    public function getRelatedAgent($agentId)
    /// Récupère tous les éléments liés à un agent
    {
        try {
            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers Cible
            $tables = array(
                'Agents' => 'idAgent',
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "SELECT Missions.codeName,
                Agents.codeAgent
                FROM Agents
                INNER JOIN AgentsInMission ON Agents.idAgent = AgentsInMission.idAgent
                INNER JOIN Missions ON AgentsInMission.idMission = Missions.idMission
                WHERE Agents.idAgent =  :AgentId";

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty($agentId) and !empty($agentId)) {
                        $stmt->bindValue(':AgentId', $agentId, PDO::PARAM_INT);
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

    public function addAgent($agentId, $agentName, $agentSpeList)
    // Ajoute un agent et lie les spécialités correspondantes
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Agents (idAgent, codeAgent)
            VALUES (:agentId, :agentName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                // on fait l'ajout dans la table Agents
                if (!empty($agentName) and !empty($agentId)) {
                    $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
                    $stmt->bindValue(':agentName', $agentName, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        // Ajout des spécialités dans la table AgentsSpecialities
                        if (is_array($agentSpeList)) {

                            // On parcours la liste des id spécialités et fait la requête
                            foreach ($agentSpeList as $specialityId) {
                                $reqSpecialities = '
                                INSERT INTO AgentsSpecialities (agent_id, speciality_id) VALUES (:agentId, :specialityId)';
                                if (is_object($bdd)) {
                                    // on teste si la connexion pdo a réussi
                                    $stmt = $bdd->prepare($reqSpecialities);

                                    // on fait l'ajout dans la table AgentsSpecialities
                                    if (!empty($agentName) and !empty($agentId)) {
                                        $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
                                        $stmt->bindValue(':specialityId', $specialityId, PDO::PARAM_STR);
                                        if (!$stmt->execute()) {
                                            return 'une erreur est survenue';
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        return 'une erreur est survenue';
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

    public function deleteAgent($agentId)
    /// Supprime le agent selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {

                // Commencer les requêtes (lot)
                $bdd->beginTransaction();

                // Supprimer la ligne correspondante dans la table AgentsSpecialities
                $stmtDeleteSpecialities = $bdd->prepare('DELETE FROM AgentsSpecialities WHERE agent_id = :agentId');
                $stmtDeleteSpecialities->bindValue(':agentId', $agentId, PDO::PARAM_INT);
                if (!$stmtDeleteSpecialities->execute()) {
                    $bdd->rollBack();
                    return 'Une erreur est survenue lors de la suppression des spécialités de l\'agent.';
                }

                $stmtDeleteAgent = $bdd->prepare('DELETE FROM Agents WHERE idAgent = :agentId');
                $stmtDeleteAgent->bindValue(':agentId', $agentId, PDO::PARAM_INT);

                if (!$stmtDeleteAgent->execute()) {
                    $bdd->rollBack();
                    return 'Une erreur est survenue lors de la suppression de l\'agent.';
                }

                // Valider la transaction
                $bdd->commit();
                return 'Cet agent a été supprimé avec succès.';

            } else {
                return 'une erreur est survenue';
            }
        } catch (Exception $e) {

            // En cas d'erreur, rollback de la transaction
            $bdd->rollBack();

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
