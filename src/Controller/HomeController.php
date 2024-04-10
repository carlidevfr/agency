<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Agent.php';
require_once './src/Model/Cible.php';
require_once './src/Model/Country.php';
require_once './src/Model/Speciality.php';
require_once './src/Model/Status.php';
require_once './src/Model/Type.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Common/Regenerate.php';

class HomeController
{
    private $Missions;
    private $Agent;
    private $Country;
    private $Speciality;
    private $Status;
    private $Type;
    private $Security;
    private $Regenerate;

    public function __construct(){
        $this->Missions = new Mission();
        $this->Agent = new Agent();
        $this->Country = new Country();
        $this->Speciality = new Speciality();
        $this->Type = new Type();
        $this->Status = new Status();
        $this->Security = new Security();
        $this->Regenerate = new Regenerate();
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

    public function apiGetCountry(){

        //récupération et envoi des pays en json
        $res = $this->Country->getAllCountryNames();
        Model::sendJSON($res) ;     
    }

    public function apiGetAgent(){

        //récupération et envoi des agents en json
        $res = $this->Agent->getAllAgentNames();
        Model::sendJSON($res) ;     
    }
    public function apiGetSpeciality(){

        //récupération et envoi des spécialités en json
        $res = $this->Speciality->getAllSpecialityNames();
        Model::sendJSON($res) ;     
    }
    public function apiGetType(){

        //récupération et envoi des types en json
        $res = $this->Type->getAllTypeNames();
        Model::sendJSON($res) ;     
    }
    public function apiGetStatus(){

        //récupération et envoi des statuts en json
        $res = $this->Status->getAllStatusNames();
        Model::sendJSON($res) ;     
    }
    public function apiGetSelectedMissions(){

        // protection xss et récupération des variables
        (isset($_GET['country'])) ? $countryId = $this->Security->filter_form($_GET['country']) : $countryId = '';
        (isset($_GET['type'])) ? $typeId = $this->Security->filter_form($_GET['type']) : $typeId = '';
        (isset($_GET['status'])) ? $statusId = $this->Security->filter_form($_GET['status']) : $statusId = '';
        (isset($_GET['speciality'])) ? $specialityId = $this->Security->filter_form($_GET['speciality']) : $specialityId = '';
        (isset($_GET['agent'])) ? $agentId = $this->Security->filter_form($_GET['agent']) : $agentId = '';
        
        //récupération et envoi du résultat en json
        $res = $this->Missions->getSelectedMissions($countryId, $typeId, $statusId, $specialityId, $agentId);
        Model::sendJSON($res) ;
    }

    public function apiGetSearchMissions(){
        
        // protection xss et récupération des variables
        (isset($_GET['search'])) ? $search = $this->Security->filter_form($_GET['search']) : $search = '';

        //récupération et envoi du résultat en json
        $res = $this->Missions->getSearchMissions($search);
        Model::sendJSON($res) ;
    }

    public function createBddProd(){
        
        // Création de la base de données prod
        if ($this->Regenerate->regenerateSqlProd('./src/Data/prod.sql')) {
            # code...
        }
    }
}
