<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Status extends Model
{
    public function getAllStatusNames()
    {
        try {
            // retourne tous les status de missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idStatus AS id, statusName AS valeur
        FROM Status';

            $stmt = $bdd->prepare($req);

            if ($stmt->execute()) {
                $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $status;
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