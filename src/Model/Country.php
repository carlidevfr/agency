<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Country extends Model
{
    public function getSearchCountryNames($countryName)
    {
        try {
            // retourne les pays recherchés
            // si vide tous les pays

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idCountry AS id, countryName AS valeur
        FROM Country
        WHERE countryName LIKE :countryName';

        $stmt = $bdd->prepare($req);

        if (!empty($countryName)) {
            $stmt->bindValue(':countryName', '%' . $countryName . '%', PDO::PARAM_STR);
            if ($stmt->execute()) {
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $contacts;
            }
        } else{
            return $this->getAllCountryNames();
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
