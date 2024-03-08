<?php

abstract class Model
{
    private static $pdo;
    public function connexionPDO(){
        try {
            self::$pdo = new PDO('mysql:host=' . Security::filter_form($_ENV["DB_HOST"]) . ';dbname=' . Security::filter_form($_ENV["DB_NAME"]).';charset=utf8mb4' , Security::filter_form($_ENV["DB_USER"]), Security::filter_form($_ENV["DB_PASS"]));
            //self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return self::$pdo;
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
        }
    }

    public static function sendJSON($info){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        echo json_encode($info);
    }
}