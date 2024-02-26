<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Common/Security.php';

class HomeController
{
    private $Missions;
    private $Security;

    public function __construct(){
        $this->Missions = new Mission();
        $this->Security = new Security();
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

        //récupération et envoi des missions en json
        $res = $this->Missions->getAllMissions();
        Model::sendJSON($res) ;     
    }

    public function apiGetSelectedMissions(){

        // protection xss et récupération des variables
        (isset($_GET['country'])) ? $countryName = $this->Security->filter_form($_GET['country']) : $countryName = '';
        (isset($_GET['type'])) ? $typeName = $this->Security->filter_form($_GET['type']) : $typeName = '';
        (isset($_GET['status'])) ? $statusName = $this->Security->filter_form($_GET['status']) : $statusName = '';
        (isset($_GET['speciality'])) ? $specialityName = $this->Security->filter_form($_GET['speciality']) : $specialityName = '';
        (isset($_GET['agent'])) ? $codeAgent = $this->Security->filter_form($_GET['agent']) : $codeAgent = '';

        //récupération et envoi du résultat en json
        $res = $this->Missions->getSelectedMissions($countryName, $typeName, $statusName, $specialityName, $codeAgent);
        Model::sendJSON($res) ;
    }
}
