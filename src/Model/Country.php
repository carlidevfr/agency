<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Country extends Model
{
    public function getAllCountryNames()
    {
        try {
            // retourne tous les pays

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idCountry AS id, countryName AS valeur
        FROM Country';

            $stmt = $bdd->prepare($req);

            if ($stmt->execute()) {
                $country = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $country;
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
