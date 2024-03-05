<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Cible extends Model
{
    public function getCiblesByIdMission($idMission)
    {
        try {
            // retourne tous les status de missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT
            Cibles.idCible,
            Cibles.isActive,
            Cibles.firstname,
            Cibles.lastname,
            Cibles.birthdate,
            Cibles.codeName,
            Country.countryName AS countryCible
        FROM
            Cibles
        JOIN
            CiblesInMission ON Cibles.idCible = CiblesInMission.idCible
        JOIN
            Missions ON CiblesInMission.idMission = Missions.idMission
        JOIN
            Country ON Cibles.countryCible = Country.idCountry
        WHERE
            Missions.idMission = :idMission';

            $stmt = $bdd->prepare($req);

            if (!empty($idMission)) {
                $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $cibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $cibles;
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
