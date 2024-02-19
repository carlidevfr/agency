<?php

function connexionPDO(){
try {
    $pdo = new PDO('mysql:dbname='.Security::filter_form($_ENV["DB_NAME"]).'; host='.Security::filter_form($_ENV["DB_HOST"]).','. Security::filter_form($_ENV["DB_USER"]) , Security::filter_form($_ENV["DB_PASS"]) );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

} catch (PDOException $e) {
    $message = 'erreur PDO avec le message : '. $e->getMessage();
}
}