<?php
require_once './vendor/autoload.php';
define("BASE_URL", '/Agency');

// inclusion des classes
require_once './app/Model/Router.php';
require_once './app/Config/env.php';
require_once './app/Controller/HomeController.php';
require_once './app/Model/Common/Security.php';
require_once './app/Model/Common/pdo.php';

$router = new Router();

$router->addRoute('GET', BASE_URL . '/', 'HomeController', 'index');

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$handler = $router->gethandler($method, $uri);
if ($handler == null) {

    header('HTTP/1.1 404 not found');
    exit();
}


$controller = new $handler['controller']();
$action = $handler['action'];
$controller->$action();
