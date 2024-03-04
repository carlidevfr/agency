<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Agent extends Model
{
    public function getAllAgentNames()
    {
        try {
            // retourne tous les status de missions au format json avec les clés 'id' et 'valeur'

            $bdd = $this->connexionPDO();
            $req = '
                SELECT idAgent AS id, codeAgent AS valeur
                FROM Agents';

            $stmt = $bdd->prepare($req);

            if ($stmt->execute()) {
                $Agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $Agents;
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
