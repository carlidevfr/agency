<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Mission extends Model
{

    public function hello()
    {
        $bdd = $this->connexionPDO();
        $req = '
        SELECT * 
        FROM Missions';

        $stmt = $bdd->prepare($req);

        if ($stmt->execute()) {
            $animaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $animaux;
            }
        //$stmt->bindValue(":idStatut",$idStatut,PDO::PARAM_INT);
    }
}
