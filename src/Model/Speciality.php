<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Speciality extends Model
{
    public function getAllSpecialityNames()
    {
        try {
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
