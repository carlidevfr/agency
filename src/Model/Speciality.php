<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Speciality extends Model
{
    public function getAllSpecialityNames()
    {
        // retourne tous les status de missions

        $bdd = $this->connexionPDO();
        $req = '
                SELECT idSpeciality AS id, speName AS valeur
                FROM Speciality';

        $stmt = $bdd->prepare($req);

        if ($stmt->execute()) {
            $Speciality = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $Speciality;
        }
    }
}
