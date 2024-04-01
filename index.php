<?php

// Sécurise le cookie de session avec httponly
session_set_cookie_params([
    'lifetime' => 600, // 10 minutes d'inactivité max
    'path' => '/',
    'domain' => $_SERVER['SERVER_NAME'],
    'httponly' => true,
    'samesite' => 'Strict', // Définir le SameSite sur Strict pour plus de sécurité
    'secure' => true // Indique que le cookie ne doit être envoyé que via une connexion HTTPS
]);

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
require_once './src/Controller/AdminTypeController.php';
require_once './src/Controller/AdminSpecialityController.php';
require_once './src/Controller/AdminPlanqueController.php';
require_once './src/Controller/AdminCibleController.php';
require_once './src/Controller/AdminAgentController.php';
require_once './src/Controller/AdminContactController.php';
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
$router->addRoute('GET', BASE_URL . '/login', 'AdminHomeController', 'adminLogin');
$router->addRoute('POST', BASE_URL . '/login', 'AdminHomeController', 'adminLogin');
$router->addRoute('GET', BASE_URL . '/logout', 'AdminHomeController', 'adminLogout');




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

$router->addRoute('GET', BASE_URL . '/admin/manage-type', 'AdminTypeController', 'adminTypePage');
$router->addRoute('POST', BASE_URL . '/admin/manage-type/add', 'AdminTypeController', 'adminAddType');
$router->addRoute('GET', BASE_URL . '/admin/manage-type/action/success', 'AdminTypeController', 'adminSuccessActionType');
$router->addRoute('POST', BASE_URL . '/admin/manage-type/delete', 'AdminTypeController', 'adminDeleteType');
$router->addRoute('GET', BASE_URL . '/admin/manage-type/update', 'AdminTypeController', 'adminUpdateTypePage');
$router->addRoute('POST', BASE_URL . '/admin/manage-type/update', 'AdminTypeController', 'adminUpdateType');

$router->addRoute('GET', BASE_URL . '/admin/manage-speciality', 'AdminSpecialityController', 'adminSpecialityPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-speciality/add', 'AdminSpecialityController', 'adminAddSpeciality');
$router->addRoute('GET', BASE_URL . '/admin/manage-speciality/action/success', 'AdminSpecialityController', 'adminSuccessActionSpeciality');
$router->addRoute('POST', BASE_URL . '/admin/manage-speciality/delete', 'AdminSpecialityController', 'adminDeleteSpeciality');
$router->addRoute('GET', BASE_URL . '/admin/manage-speciality/update', 'AdminSpecialityController', 'adminUpdateSpecialityPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-speciality/update', 'AdminSpecialityController', 'adminUpdateSpeciality');

$router->addRoute('GET', BASE_URL . '/admin/manage-planque', 'AdminPlanqueController', 'adminPlanquePage');
$router->addRoute('POST', BASE_URL . '/admin/manage-planque/add', 'AdminPlanqueController', 'adminAddPlanque');
$router->addRoute('GET', BASE_URL . '/admin/manage-planque/action/success', 'AdminPlanqueController', 'adminSuccessActionPlanque');
$router->addRoute('POST', BASE_URL . '/admin/manage-planque/delete', 'AdminPlanqueController', 'adminDeletePlanque');
$router->addRoute('GET', BASE_URL . '/admin/manage-planque/update', 'AdminPlanqueController', 'adminUpdatePlanquePage');
$router->addRoute('POST', BASE_URL . '/admin/manage-planque/update', 'AdminPlanqueController', 'adminUpdatePlanque');

$router->addRoute('GET', BASE_URL . '/admin/manage-cible', 'AdminCibleController', 'adminCiblePage');
$router->addRoute('POST', BASE_URL . '/admin/manage-cible/add', 'AdminCibleController', 'adminAddCible');
$router->addRoute('GET', BASE_URL . '/admin/manage-cible/action/success', 'AdminCibleController', 'adminSuccessActionCible');
$router->addRoute('POST', BASE_URL . '/admin/manage-cible/delete', 'AdminCibleController', 'adminDeleteCible');
$router->addRoute('GET', BASE_URL . '/admin/manage-cible/update', 'AdminCibleController', 'adminUpdateCiblePage');
$router->addRoute('POST', BASE_URL . '/admin/manage-cible/update', 'AdminCibleController', 'adminUpdateCible');

$router->addRoute('GET', BASE_URL . '/admin/manage-contact', 'AdminContactController', 'adminContactPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-contact/add', 'AdminContactController', 'adminAddContact');
$router->addRoute('GET', BASE_URL . '/admin/manage-contact/action/success', 'AdminContactController', 'adminSuccessActionContact');
$router->addRoute('POST', BASE_URL . '/admin/manage-contact/delete', 'AdminContactController', 'adminDeleteContact');

$router->addRoute('GET', BASE_URL . '/admin/manage-agent', 'AdminAgentController', 'adminAgentPage');
$router->addRoute('POST', BASE_URL . '/admin/manage-agent/add', 'AdminAgentController', 'adminAddAgent');
$router->addRoute('GET', BASE_URL . '/admin/manage-agent/action/success', 'AdminAgentController', 'adminSuccessActionAgent');
$router->addRoute('POST', BASE_URL . '/admin/manage-agent/delete', 'AdminAgentController', 'adminDeleteAgent');

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
