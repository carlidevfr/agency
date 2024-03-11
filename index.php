<?php
session_start();

require_once './vendor/autoload.php';
define("BASE_URL", '/agency');

// inclusion des classes
require_once './src/Model/Common/Router.php';
require_once './src/Config/env.php';
require_once './src/Controller/HomeController.php';
require_once './src/Controller/DisplayMissionsController.php';
require_once './src/Controller/AdminHomeController.php';
require_once './src/Controller/AdminCountryController.php';
require_once './src/Controller/AdminStatusController.php';
require_once './src/Model/Common/Security.php';

$router = new Router();

$router->addRoute('GET', BASE_URL . '/', 'homecontroller', 'index');

$router->addRoute('GET', BASE_URL . '/apigetmissions', 'HomeController', 'apiGetMissions');
$router->addRoute('GET', BASE_URL . '/apigetcountry', 'HomeController', 'apiGetCountry');
$router->addRoute('GET', BASE_URL . '/apigetagent', 'HomeController', 'apiGetAgent');
$router->addRoute('GET', BASE_URL . '/apigetspeciality', 'HomeController', 'apiGetSpeciality');
$router->addRoute('GET', BASE_URL . '/apigettype', 'HomeController', 'apiGetType');
$router->addRoute('GET', BASE_URL . '/apigetstatus', 'HomeController', 'apiGetStatus');
$router->addRoute('GET', BASE_URL . '/apigetselectedmissions', 'HomeController', 'apiGetSelectedMissions');
$router->addRoute('GET', BASE_URL . '/apigetsearchmissions', 'HomeController', 'apiGetSearchMissions');

$router->addRoute('GET', BASE_URL . '/mission', 'DisplayMissionsController', 'getMission');

$router->addRoute('GET', BASE_URL . '/admin', 'AdminHomeController', 'adminHomePage');

$router->addRoute('GET', BASE_URL . '/admin/manage-country', 'AdminCountryController', 'adminCountryPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-country/add', 'AdminCountryController', 'adminAddCountry');
$router->addRoute('GET', BASE_URL . '/admin/manage-country/action/success', 'AdminCountryController', 'adminSuccessActionCountry');
$router->addRoute('POST', BASE_URL . '/admin/manage-country/delete', 'AdminCountryController', 'adminDeleteCountry');
$router->addRoute('GET', BASE_URL . '/admin/manage-country/update', 'AdminCountryController', 'adminUpdateCountryPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-country/update', 'AdminCountryController', 'adminUpdateCountry');

$router->addRoute('GET', BASE_URL . '/admin/manage-status', 'AdminStatusController', 'adminStatusPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-status/add', 'AdminStatusController', 'adminAddStatus');
$router->addRoute('GET', BASE_URL . '/admin/manage-status/action/success', 'AdminStatusController', 'adminSuccessActionStatus');
$router->addRoute('POST', BASE_URL . '/admin/manage-status/delete', 'AdminStatusController', 'adminDeleteStatus');
$router->addRoute('GET', BASE_URL . '/admin/manage-status/update', 'AdminStatusController', 'adminUpdateStatusPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-status/update', 'AdminStatusController', 'adminUpdateStatus');


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
