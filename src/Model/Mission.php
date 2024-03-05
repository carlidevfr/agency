<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Mission extends Model
{

    public function getAllMissions()
    {
        try {
            // retourne toutes les missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT * 
        FROM Missions';

            $stmt = $bdd->prepare($req);

            if ($stmt->execute()) {
                $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $missions;
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

    public function getMission($idMission)
    {
        try {
            // retourne toutes les missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT Missions.idMission,
        Missions.title,
        Missions.codeName,
        Missions.description,
        Missions.beginDate,
        Missions.endDate,
        Country.countryName,
        Types.typeName,
        Status.statusName,
        Speciality.speName
        FROM Missions
        JOIN Country ON Missions.missionCountry = Country.idCountry
        JOIN Types ON Missions.missionType = Types.idType
        JOIN Status ON Missions.missionStatus = Status.idStatus
        JOIN Speciality ON Missions.missionSpeciality = Speciality.idSpeciality
        WHERE Missions.idMission = :idMission';

            $stmt = $bdd->prepare($req);

            if (!empty($idMission)) {
                $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $mission = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $mission;
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

    public function getSelectedMissions($countryId, $typeId, $statusId, $specialityId, $agentId)
    {
        /*
        param : id Pays, Type, status, spécialité, agent de la mission
        return : missions correspondantes
        */
        try {
            // Connexion à la base de données
            $bdd = $this->connexionPDO();

            // Requête SQL de base avec des jointures
            $req = "SELECT
            Missions.idMission,
            Missions.title,
            Missions.codeName,
            Missions.description,
            Missions.beginDate,
            Missions.endDate,
            Country.countryName,
            Types.typeName,
            Status.statusName,
            Speciality.speName,
            Agents.codeAgent,
            GROUP_CONCAT(Agents.codeAgent) AS agentNames
        FROM Missions
        JOIN Country ON Missions.missionCountry = Country.idCountry
        JOIN Types ON Missions.missionType = Types.idType
        JOIN Status ON Missions.missionStatus = Status.idStatus
        JOIN Speciality ON Missions.missionSpeciality = Speciality.idSpeciality
        JOIN AgentsInMission ON Missions.idMission = AgentsInMission.idMission
        JOIN Agents ON AgentsInMission.idAgent = Agents.idAgent";

            // Construction dynamique de la clause WHERE
            $conditions = [];

            if (!empty($countryId)) {
                $conditions[] = "Country.idCountry = :countryId";
            }

            if (!empty($typeId)) {
                $conditions[] = "Types.idType = :typeId";
            }

            if (!empty($statusId)) {
                $conditions[] = "Status.idStatus = :statusId";
            }

            if (!empty($specialityId)) {
                $conditions[] = "Speciality.idSpeciality = :specialityId";
            }

            if (!empty($agentId)) {
                $conditions[] = "Agents.idAgent = :agentId";
            }

            // Ajout de la clause WHERE à la requête si nécessaire
            if (!empty($conditions)) {
                $req .= " WHERE " . implode(" AND ", $conditions);
            } 
            $req .= " GROUP BY Missions.idMission" ;

            
            // Préparation de la requête
            $stmt = $bdd->prepare($req);

            // Liaison des paramètres
            if (!empty($countryId)) {
                $stmt->bindValue(':countryId', $countryId, PDO::PARAM_STR);
            }

            if (!empty($typeId)) {
                $stmt->bindValue(':typeId', $typeId, PDO::PARAM_STR);
            }

            if (!empty($statusId)) {
                $stmt->bindValue(':statusId', $statusId, PDO::PARAM_STR);
            }

            if (!empty($specialityId)) {
                $stmt->bindValue(':specialityId', $specialityId, PDO::PARAM_STR);
            }

            if (!empty($agentId)) {
                $stmt->bindValue(':agentId', $agentId, PDO::PARAM_STR);
            }

            // Exécution de la requête
            if ($stmt->execute()) {
                $missionsFiltered = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $missionsFiltered;
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


    public function getSearchMissions($search)
    {
        /*
        param : id Pays, Type, status, spécialité, agent de la mission
        return : missions correspondantes
        */

        try {
            // Connexion à la base de données
            $bdd = $this->connexionPDO();

            // Requête SQL de base avec des jointures
            $req = "SELECT
            Missions.idMission,
            Missions.title,
            Missions.codeName,
            Missions.description,
            Missions.beginDate,
            Missions.endDate,
            Country.countryName,
            Types.typeName,
            Status.statusName,
            Speciality.speName,
            Agents.codeAgent,
            GROUP_CONCAT(Agents.codeAgent) AS agentNames
        FROM Missions
        JOIN Country ON Missions.missionCountry = Country.idCountry
        JOIN Types ON Missions.missionType = Types.idType
        JOIN Status ON Missions.missionStatus = Status.idStatus
        JOIN Speciality ON Missions.missionSpeciality = Speciality.idSpeciality
        JOIN AgentsInMission ON Missions.idMission = AgentsInMission.idMission
        JOIN Agents ON AgentsInMission.idAgent = Agents.idAgent
        WHERE 
            Missions.title LIKE :searchTerm OR
            Missions.codeName LIKE :searchTerm OR
            Agents.codeAgent LIKE :searchTerm OR
            Types.typeName LIKE :searchTerm OR
            Status.statusName LIKE :searchTerm OR
            Speciality.speName LIKE :searchTerm OR
            Country.countryName LIKE :searchTerm
        GROUP BY Missions.idMission";


            // Préparation de la requête
            $stmt = $bdd->prepare($req);

            // Liaison des paramètres
            if (!empty($search)) {
                $stmt->bindValue(':searchTerm', '%' . $search . '%', PDO::PARAM_STR);

                // Exécution de la requête
                if ($stmt->execute()) {
                    $missionsSearch = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $missionsSearch;
                }

            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- h:m:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }
}