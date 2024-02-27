<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Type extends Model
{
    public function getAllTypesNames()
    {
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
    }
}
