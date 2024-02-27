<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Mission extends Model
{

    public function getAllMissions()
    {
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
    }

    public function getSelectedMissions($countryId, $typeId, $statusId, $specialityId, $agentId)
    {
        /*
        param : id Pays, Type, status, spécialité, agent de la mission
        return : missions correspondantes
        */

        // Connexion à la base de données
        $bdd = $this->connexionPDO();

        // Requête SQL de base avec des jointures
        $req = "SELECT
            Missions.title,
            Missions.codeName,
            Missions.description,
            Missions.beginDate,
            Missions.endDate,
            Country.countryName,
            Types.typeName,
            Status.statusName,
            Speciality.speName,
            Agents.codeAgent
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
    }


public function getSearchMissions($search)
    {
        /*
        param : id Pays, Type, status, spécialité, agent de la mission
        return : missions correspondantes
        */

        // Connexion à la base de données
        $bdd = $this->connexionPDO();

        // Requête SQL de base avec des jointures
        $req = "SELECT
            Missions.title,
            Missions.codeName,
            Missions.description,
            Missions.beginDate,
            Missions.endDate,
            Country.countryName,
            Types.typeName,
            Status.statusName,
            Speciality.speName,
            Agents.codeAgent
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
            Country.countryName LIKE :searchTerm";
            
            
        // Préparation de la requête
        $stmt = $bdd->prepare($req);

        // Liaison des paramètres
        if (!empty($search)) {
            $stmt->bindValue(':searchTerm', '%'. $search .'%', PDO::PARAM_STR);

        // Exécution de la requête
        if ($stmt->execute()) {
            $missionsSearch = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $missionsSearch;
        }
    }
}
}