<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Agent.php';
require_once './src/Model/Cible.php';
require_once './src/Model/Country.php';
require_once './src/Model/Planque.php';
require_once './src/Model/Contact.php';
require_once './src/Model/Speciality.php';
require_once './src/Model/Status.php';
require_once './src/Model/Type.php';
require_once './src/Model/Common/Security.php';

class DisplayMissionsController
{
    private $Missions;
    private $Agent;
    private $Country;
    private $Speciality;
    private $Status;
    private $Type;
    private $Security;
    private $Cible;
    private $Planque;
    private $Contact;


    public function __construct(){
        $this->Missions = new Mission();
        $this->Agent = new Agent();
        $this->Country = new Country();
        $this->Speciality = new Speciality();
        $this->Type = new Type();
        $this->Status = new Status();
        $this->Security = new Security();
        $this->Cible = new Cible();
        $this->Planque = new Planque();
        $this->Contact = new Contact();
    }

    public function getMission(){
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('displayMission.twig');
        // On récupère l'id de mission
        (isset($_GET['idMission'])) ? $idMission = $this->Security->filter_form($_GET['idMission']) : $idMission = '';
        $mission = $this->Missions->getMission($idMission);
        $cibles = $this->Cible->getCiblesByIdMission($idMission);
        $planques = $this->Planque->getPlanquesByIdMission($idMission);
        $contacts = $this->Contact->getContactsByIdMission($idMission);
        $agents = $this->Agent->getAgentsByIdMission($idMission);

        echo  $template->render([
            'base_url' => BASE_URL,
            'mission' => $mission,
            'cibles'=> $cibles,
            'planques'=> $planques,
            'contacts'=>$contacts,
            'agents'=> $agents
        ]);
    }

}
