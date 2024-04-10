<?php
require_once './src/Model/Common/Model.php';

class Regenerate extends Model
{
    public function regenerateSqlProd($chemin_sql) {
        try {
            $pdo = $this->connexionPDO();
            $sql = file_get_contents($chemin_sql);
            $pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            // Gérer les erreurs
            return false;
        }
    }
}
