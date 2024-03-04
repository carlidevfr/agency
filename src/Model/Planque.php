<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Planque extends Model
{
    public function getPlanquesByIdMission($idMission)
    {
        try {
            // retourne tous les status de missions

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
}
