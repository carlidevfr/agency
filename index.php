<?php
require_once './vendor/autoload.php';
define("BASE_URL", '/agency');

// inclusion des classes
require_once './src/Model/Router.php';
require_once './src/Config/env.php';
require_once './src/Controller/HomeController.php';
require_once './src/Model/Common/Security.php';


$router = new Router();

$router->addRoute('GET', BASE_URL . '/', 'homecontroller', 'index');
$router->addRoute('GET', BASE_URL . '/apigetmissions', 'HomeController', 'apiGetMissions');
$router->addRoute('GET', BASE_URL . '/apigetselectedmissions', 'HomeController', 'apiGetSelectedMissions');

//var_dump($router->getRoutes());
//var_dump($_SERVER['REQUEST_URI']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = strtolower($_SERVER['REQUEST_URI']); // gère les minuscules et les majuscules

$handler = $router->gethandler($method, $uri);
if ($handler == null) {

    header('HTTP/1.1 404 not found');
    echo '404';
    exit();
}


$controller = new $handler['controller']();
$action = $handler['action'];
$controller->$action();
