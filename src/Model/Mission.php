<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Mission extends Model
{

    public function getAllMissions()
    {
        // return all missions

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

    public function getSelectedMissions($countryName, $typeName, $statusName, $specialityName, $codeAgent)
    {
        /*
        param : Pays, Type, status, spécialité, agent de la mission
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

        if (!empty($countryName)) {
            $conditions[] = "Country.countryName = :countryName";
        }

        if (!empty($typeName)) {
            $conditions[] = "Types.typeName = :typeName";
        }

        if (!empty($statusName)) {
            $conditions[] = "Status.statusName = :statusName";
        }

        if (!empty($specialityName)) {
            $conditions[] = "Speciality.speName = :specialityName";
        }

        if (!empty($codeAgent)) {
            $conditions[] = "Agents.codeAgent = :codeAgent";
        }

        // Ajout de la clause WHERE à la requête si nécessaire
        if (!empty($conditions)) {
            $req .= " WHERE " . implode(" AND ", $conditions);
        }
        // Préparation de la requête
        $stmt = $bdd->prepare($req);

        // Liaison des paramètres
        if (!empty($countryName)) {
            $stmt->bindValue(':countryName', $countryName, PDO::PARAM_STR);
        }

        if (!empty($typeName)) {
            $stmt->bindValue(':typeName', $typeName, PDO::PARAM_STR);
        }

        if (!empty($statusName)) {
            $stmt->bindValue(':statusName', $statusName, PDO::PARAM_STR);
        }

        if (!empty($specialityName)) {
            $stmt->bindValue(':specialityName', $specialityName, PDO::PARAM_STR);
        }

        if (!empty($codeAgent)) {
            $stmt->bindValue(':codeAgent', $codeAgent, PDO::PARAM_STR);
        }

        // Exécution de la requête
        if ($stmt->execute()) {
            $missionsFiltered = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $missionsFiltered;
        }
    }
}
