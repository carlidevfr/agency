<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Agent extends Model
{
    public function getAllAgentNames()
    {
        try {
            // retourne tous les status de missions au format json avec les clés 'id' et 'valeur'

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
    {
        try {
            // retourne tous les status de missions

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
}
