<?php
require_once './src/Model/Mission.php';

class HomeController
{
    private $Missions;

    public function __construct(){
        $this->Missions = new Mission();
    }

    public function index(){
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);

        $template = $twig->load('home.twig');
        echo  $template->render([
            'base_url' => BASE_URL,
        ]);
    }

    public function apiGetMissions(){
        $res = $this->Missions->hello();
        //echo json_encode($res);
        Model::sendJSON($res) ;     
    }
}
