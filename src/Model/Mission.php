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
            $req .= " GROUP BY Missions.idMission";


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

    public function getPaginationAllMissionNames($page, $itemsPerPage)
    {
        try {
            // retourne toutes les missions triées par page

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "SELECT
            Missions.idMission AS id,
            Missions.title,
            Missions.codeName AS valeur,
            Missions.description,
            DATE_FORMAT(Missions.beginDate, '%d/%m/%Y') AS dateBegin,
            DATE_FORMAT(Missions.endDate, '%d/%m/%Y') AS dateEnd,
            Country.countryName AS missionCountryName,
            Types.typeName AS missionTypeName,
            Status.statusName AS missionStatusName,
            Speciality.speName AS missionSpecialityName,
            GROUP_CONCAT(DISTINCT Agents.codeAgent) AS agentNames,
            GROUP_CONCAT(DISTINCT Cibles.idCible) AS cibleIds,
            GROUP_CONCAT(DISTINCT Cibles.codeName) AS cibleNames,
            GROUP_CONCAT(DISTINCT Contacts.idContact) AS contactIds,
            GROUP_CONCAT(DISTINCT Planques.planqueName) AS planqueNames,
            GROUP_CONCAT(DISTINCT CONCAT(Cibles.firstname, ' ', Cibles.lastname)) AS contactNames
        FROM
            Missions
        JOIN
            Country ON Missions.missionCountry = Country.idCountry
        JOIN
            Types ON Missions.missionType = Types.idType
        JOIN
            Status ON Missions.missionStatus = Status.idStatus
        JOIN
            Speciality ON Missions.missionSpeciality = Speciality.idSpeciality
        JOIN
            AgentsInMission ON Missions.idMission = AgentsInMission.idMission
        JOIN
            Agents ON AgentsInMission.idAgent = Agents.idAgent
        LEFT JOIN
            CiblesInMission ON Missions.idMission = CiblesInMission.idMission
        LEFT JOIN
            Cibles ON CiblesInMission.idCible = Cibles.idCible
        LEFT JOIN
            (SELECT idContact, idMission FROM ContactsInMission GROUP BY idContact, idMission) AS MissionContacts ON Missions.idMission = MissionContacts.idMission
        LEFT JOIN
            Contacts ON MissionContacts.idContact = Contacts.idContact
        LEFT JOIN
            Planques ON Missions.idMission = Planques.actuallyMission
        GROUP BY
            Missions.idMission
        ORDER BY
            Missions.idMission
        LIMIT
            :offset, :itemsPerPage";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $country = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $country;
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

    public function getSearchMissionNames($missionName, $page, $itemsPerPage)
    // retourne les missions recherchées
    // si vide toutes les missions
    {
        try {
            // Calculer l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "SELECT
            Missions.idMission AS id,
            Missions.title,
            Missions.codeName AS valeur,
            Missions.description,
            DATE_FORMAT(Missions.beginDate, '%d/%m/%Y') AS dateBegin,
            DATE_FORMAT(Missions.endDate, '%d/%m/%Y') AS dateEnd,
            Country.countryName AS missionCountryName,
            Types.typeName AS missionTypeName,
            Status.statusName AS missionStatusName,
            Speciality.speName AS missionSpecialityName,
            GROUP_CONCAT(DISTINCT Agents.codeAgent) AS agentNames,
            GROUP_CONCAT(DISTINCT Cibles.idCible) AS cibleIds,
            GROUP_CONCAT(DISTINCT Cibles.codeName) AS cibleNames,
            GROUP_CONCAT(DISTINCT Contacts.idContact) AS contactIds,
            GROUP_CONCAT(DISTINCT Planques.planqueName) AS planqueNames,
            GROUP_CONCAT(DISTINCT CONCAT(Cibles.firstname, ' ', Cibles.lastname)) AS contactNames
        FROM
            Missions
        JOIN
            Country ON Missions.missionCountry = Country.idCountry
        JOIN
            Types ON Missions.missionType = Types.idType
        JOIN
            Status ON Missions.missionStatus = Status.idStatus
        JOIN
            Speciality ON Missions.missionSpeciality = Speciality.idSpeciality
        JOIN
            AgentsInMission ON Missions.idMission = AgentsInMission.idMission
        JOIN
            Agents ON AgentsInMission.idAgent = Agents.idAgent
        LEFT JOIN
            CiblesInMission ON Missions.idMission = CiblesInMission.idMission
        LEFT JOIN
            Cibles ON CiblesInMission.idCible = Cibles.idCible
        LEFT JOIN
            (SELECT idContact, idMission FROM ContactsInMission GROUP BY idContact, idMission) AS MissionContacts ON Missions.idMission = MissionContacts.idMission
        LEFT JOIN
            Contacts ON MissionContacts.idContact = Contacts.idContact
        LEFT JOIN
            Planques ON Missions.idMission = Planques.actuallyMission
        WHERE 
            Missions.title LIKE :searchTerm OR
            Missions.codeName LIKE :searchTerm OR
            Agents.codeAgent LIKE :searchTerm OR
            Types.typeName LIKE :searchTerm OR
            Status.statusName LIKE :searchTerm OR
            Speciality.speName LIKE :searchTerm OR
            Cibles.codeName LIKE :searchTerm OR
            Country.countryName LIKE :searchTerm
        GROUP BY
            Missions.idMission
        LIMIT
            :offset, :itemsPerPage";

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($missionName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':searchTerm', '%' . $missionName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $mission = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $mission;
                    }
                } else {
                    return $this->getAllMissionNames();
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

    public function getAllMissionNames()
    // retourne toutes les missions
    {
        try {

            $bdd = $this->connexionPDO();
            $req = "SELECT
            Missions.idMission AS id,
            Missions.title,
            Missions.codeName AS valeur,
            Missions.description,
            DATE_FORMAT(Missions.beginDate, '%d/%m/%Y') AS dateBegin,
            DATE_FORMAT(Missions.endDate, '%d/%m/%Y') AS dateEnd,
            Country.countryName AS missionCountryName,
            Types.typeName AS missionTypeName,
            Status.statusName AS missionStatusName,
            Speciality.speName AS missionSpecialityName,
            GROUP_CONCAT(DISTINCT Agents.codeAgent) AS agentNames,
            GROUP_CONCAT(DISTINCT Cibles.idCible) AS cibleIds,
            GROUP_CONCAT(DISTINCT Cibles.codeName) AS cibleNames,
            GROUP_CONCAT(DISTINCT Contacts.idContact) AS contactIds,
            GROUP_CONCAT(DISTINCT Planques.planqueName) AS planqueNames,
            GROUP_CONCAT(DISTINCT CONCAT(Cibles.firstname, ' ', Cibles.lastname)) AS contactNames
        FROM
            Missions
        JOIN
            Country ON Missions.missionCountry = Country.idCountry
        JOIN
            Types ON Missions.missionType = Types.idType
        JOIN
            Status ON Missions.missionStatus = Status.idStatus
        JOIN
            Speciality ON Missions.missionSpeciality = Speciality.idSpeciality
        JOIN
            AgentsInMission ON Missions.idMission = AgentsInMission.idMission
        JOIN
            Agents ON AgentsInMission.idAgent = Agents.idAgent
        LEFT JOIN
            CiblesInMission ON Missions.idMission = CiblesInMission.idMission
        LEFT JOIN
            Cibles ON CiblesInMission.idCible = Cibles.idCible
        LEFT JOIN
            (SELECT idContact, idMission FROM ContactsInMission GROUP BY idContact, idMission) AS MissionContacts ON Missions.idMission = MissionContacts.idMission
        LEFT JOIN
            Contacts ON MissionContacts.idContact = Contacts.idContact
        LEFT JOIN
            Planques ON Missions.idMission = Planques.actuallyMission
        GROUP BY
            Missions.idMission
        ORDER BY
            Missions.idMission";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);


                if ($stmt->execute()) {
                    $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $missions;
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

    public function deleteMission($idMission)
    {
        try {
            $bdd = $this->connexionPDO();

            // Démarre une transaction
            $bdd->beginTransaction();

            // Suppression des liens avec les contacts
            $stmt = $bdd->prepare("DELETE FROM ContactsInMission WHERE idMission = :idMission");
            $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
            $stmt->execute();

            // Suppression des liens avec les cibles
            $stmt = $bdd->prepare("DELETE FROM CiblesInMission WHERE idMission = :idMission");
            $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
            $stmt->execute();

            // Suppression des liens avec les agents
            $stmt = $bdd->prepare("DELETE FROM AgentsInMission WHERE idMission = :idMission");
            $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
            $stmt->execute();

            // Mise à jour des planques
            $stmt = $bdd->prepare("UPDATE Planques SET actuallyMission = NULL WHERE actuallyMission = :idMission");
            $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
            $stmt->execute();

            // Suppression de la mission
            $stmt = $bdd->prepare("DELETE FROM Missions WHERE idMission = :idMission");
            $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
            $stmt->execute();

            // Valide la transaction
            $bdd->commit();

            return "Mission supprimée avec succès.";
        } catch (Exception $e) {
            // En cas d'erreur, annule la transaction
            $bdd->rollBack();

            // Gestion des erreurs
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            return "Une erreur est survenue lors de la suppression de la mission.";
        }
    }

    public function addMission($title, $codeName, $description, $beginDate, $endDate, $missionCountry, $missionType, $missionStatus, $missionSpeciality, $cibleIds, $contactIds, $agentIds, $planqueIds)
    {
        try {
            $bdd = $this->connexionPDO();

            // Début de la transaction
            $bdd->beginTransaction();

            // Requête SQL pour insérer une nouvelle mission
            $req = "INSERT INTO Missions (title, codeName, description, beginDate, endDate, missionCountry, missionType, missionStatus, missionSpeciality) 
                VALUES (:title, :codeName, :description, :beginDate, :endDate, :missionCountry, :missionType, :missionStatus, :missionSpeciality)";

            // Préparation de la requête
            $stmt = $bdd->prepare($req);

            // Liaison des paramètres
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':codeName', $codeName, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':beginDate', $beginDate, PDO::PARAM_STR);
            $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
            $stmt->bindValue(':missionCountry', $missionCountry, PDO::PARAM_INT);
            $stmt->bindValue(':missionType', $missionType, PDO::PARAM_INT);
            $stmt->bindValue(':missionStatus', $missionStatus, PDO::PARAM_INT);
            $stmt->bindValue(':missionSpeciality', $missionSpeciality, PDO::PARAM_INT);

            // Exécution de la requête
            if ($stmt->execute()) {
                // Récupération de l'ID de la mission nouvellement créée
                $missionId = $bdd->lastInsertId();

                // Ajout des cibles associées à la mission dans la table CiblesInMission
                foreach ($cibleIds as $cibleId) {
                    $reqCible = "INSERT INTO CiblesInMission (idCible, idMission) VALUES (:idCible, :idMission)";
                    $stmtCible = $bdd->prepare($reqCible);
                    $stmtCible->bindValue(':idCible', $cibleId, PDO::PARAM_INT);
                    $stmtCible->bindValue(':idMission', $missionId, PDO::PARAM_INT);
                    if (!$stmtCible->execute()) {
                        $bdd->rollBack(); // Annuler la transaction en cas d'échec
                        return "Une erreur est survenue lors de la création de mission.";
                    }
                }

                // Ajout des contacts associés à la mission dans la table ContactsInMission
                foreach ($contactIds as $contactId) {
                    $reqContact = "INSERT INTO ContactsInMission (idContact, idMission) VALUES (:idContact, :idMission)";
                    $stmtContact = $bdd->prepare($reqContact);
                    $stmtContact->bindValue(':idContact', $contactId, PDO::PARAM_INT);
                    $stmtContact->bindValue(':idMission', $missionId, PDO::PARAM_INT);
                    if (!$stmtContact->execute()) {
                        $bdd->rollBack(); // Annuler la transaction en cas d'échec
                        return "Une erreur est survenue lors de la création de mission.";
                    }
                }

                // Ajout des agents associés à la mission dans la table AgentsInMission
                foreach ($agentIds as $agentId) {
                    $reqAgent = "INSERT INTO AgentsInMission (idAgent, idMission) VALUES (:idAgent, :idMission)";
                    $stmtAgent = $bdd->prepare($reqAgent);
                    $stmtAgent->bindValue(':idAgent', $agentId, PDO::PARAM_INT);
                    $stmtAgent->bindValue(':idMission', $missionId, PDO::PARAM_INT);
                    if (!$stmtAgent->execute()) {
                        $bdd->rollBack(); // Annuler la transaction en cas d'échec
                        return "Une erreur est survenue lors de la création de mission.";
                    }
                }

                // Ajout des identifiants de mission sur les planques associées à la mission
                foreach ($planqueIds as $planqueId) {
                    $reqPlanque = "UPDATE Planques SET actuallyMission = :idMission WHERE idPlanque = :idPlanque";
                    $stmtPlanque = $bdd->prepare($reqPlanque);
                    $stmtPlanque->bindValue(':idMission', $missionId, PDO::PARAM_INT);
                    $stmtPlanque->bindValue(':idPlanque', $planqueId, PDO::PARAM_INT);
                    if (!$stmtPlanque->execute()) {
                        $bdd->rollBack(); // Annuler la transaction en cas d'échec
                        return "Une erreur est survenue lors de la création de mission.";
                    }
                }

                // Si tout s'est bien passé, on valide la transaction
                $bdd->commit();
                return "La mission a bien été créée.";
            } else {
                // Annuler la transaction en cas d'échec
                $bdd->rollBack();
                return "Une erreur est survenue lors de la création de mission.";
            }
        } catch (Exception $e) {
            // Gestion des erreurs
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            // Annuler la transaction en cas d'erreur
            $bdd->rollBack();
            return "Une erreur est survenue lors de la création de mission.";
        }
    }

    public function verifyMissionConstraints($missionCountry, $missionSpeciality, $cibleIds, $contactIds, $agentIds, $planqueIds)
    {
        try {
            $bdd = $this->connexionPDO();

            // Vérifier si les cibles ont une nationalité différente de celle des agents
            $reqCheckCiblesAgents = "SELECT COUNT(*) AS count
                                 FROM Cibles
                                 JOIN Agents ON Cibles.countryCible = Agents.countryCible
                                 WHERE Cibles.idCible IN (" . implode(",", $cibleIds) . ")";
            $stmtCheckCiblesAgents = $bdd->query($reqCheckCiblesAgents);
            $resultCheckCiblesAgents = $stmtCheckCiblesAgents->fetch(PDO::FETCH_ASSOC);
            if ($resultCheckCiblesAgents['count'] > 0) {
                return false; // Contrainte violée
            }

            // Vérifier si les contacts ont la nationalité du pays de la mission
            $reqCheckContacts = "SELECT COUNT(*) AS count
                             FROM Contacts
                             JOIN Country ON Contacts.countryCible = Country.idCountry
                             WHERE Contacts.idContact IN (" . implode(",", $contactIds) . ") AND Country.idCountry != :missionCountry";
            $stmtCheckContacts = $bdd->prepare($reqCheckContacts);
            $stmtCheckContacts->bindValue(':missionCountry', $missionCountry, PDO::PARAM_INT);
            $stmtCheckContacts->execute();
            $resultCheckContacts = $stmtCheckContacts->fetch(PDO::FETCH_ASSOC);
            if ($resultCheckContacts['count'] > 0) {
                return false; // Contrainte violée
            }

            // Vérifier si la planque est dans le même pays que la mission
            $reqCheckPlanque = "SELECT COUNT(*) AS count
                            FROM Planques
                            WHERE Planques.idPlanque IN (" . implode(",", $planqueIds) . ") AND Planques.planqueCountry != :missionCountry";
            $stmtCheckPlanque = $bdd->prepare($reqCheckPlanque);
            $stmtCheckPlanque->bindValue(':missionCountry', $missionCountry, PDO::PARAM_INT);
            $stmtCheckPlanque->execute();
            $resultCheckPlanque = $stmtCheckPlanque->fetch(PDO::FETCH_ASSOC);
            if ($resultCheckPlanque['count'] > 0) {
                return false; // Contrainte violée
            }

            // Vérifier s'il y a au moins un agent avec la spécialité de mission requise
            $reqCheckAgentsSpeciality = "SELECT COUNT(*) AS count
                                      FROM Agents
                                      JOIN AgentsSpecialities ON Agents.idAgent = AgentsSpecialities.agent_id
                                      WHERE Agents.idAgent IN (" . implode(",", $agentIds) . ") AND AgentsSpecialities.speciality_id = :missionSpeciality";
            $stmtCheckAgentsSpeciality = $bdd->prepare($reqCheckAgentsSpeciality);
            $stmtCheckAgentsSpeciality->bindValue(':missionSpeciality', $missionSpeciality, PDO::PARAM_INT);
            $stmtCheckAgentsSpeciality->execute();
            $resultCheckAgentsSpeciality = $stmtCheckAgentsSpeciality->fetch(PDO::FETCH_ASSOC);
            if ($resultCheckAgentsSpeciality['count'] == 0) {
                return false; // Contrainte violée
            }

            // Si toutes les contraintes sont respectées, retourner true
            return true;

        } catch (Exception $e) {
            // Gestion des erreurs
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            return false;
        }
    }


}