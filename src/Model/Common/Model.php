<?php

abstract class Model
{
    private static $pdo;
    protected function connexionPDO(){
        try {
            self::$pdo = new PDO('mysql:host=' . Security::filter_form($_ENV["DB_HOST"]) . ';dbname=' . Security::filter_form($_ENV["DB_NAME"]).';charset=utf8mb4' , Security::filter_form($_ENV["DB_USER"]), Security::filter_form($_ENV["DB_PASS"]));
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return self::$pdo;
        } catch (PDOException $e) {
            $message = 'erreur PDO avec le message : ' . $e->getMessage();
            return $message;
        }
    }

    public static function sendJSON($info){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        echo json_encode($info);
    }
}