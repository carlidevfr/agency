<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Country extends Model
{
    public function getAllCountryNames()
    {
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
    }
}
