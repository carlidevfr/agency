<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Type extends Model
{
    public function getAllTypesNames()
    {
        try {
            // retourne tous les types de missions

            $bdd = $this->connexionPDO();
            $req = '
                SELECT idType AS id, typeName AS valeur
                FROM Types';

            $stmt = $bdd->prepare($req);

            if ($stmt->execute()) {
                $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $types;
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
