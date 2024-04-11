<?php
require_once './src/Model/Common/Model.php';

class Regenerate extends Model
{
    public function regenerateSqlProd($chemin_sql)
    {
        try {
            $pdo = $this->connexionPDO();
            $sql = file_get_contents($chemin_sql);
            $pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
            return false;
        }
    }
}
