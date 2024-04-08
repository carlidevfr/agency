<?php

require_once './src/Model/Mission.php';
require_once './src/Model/Agent.php';
require_once './src/Model/Country.php';
require_once './src/Model/Planque.php';
require_once './src/Model/Contact.php';
require_once './src/Model/Cible.php';
require_once './src/Model/Type.php';
require_once './src/Model/Speciality.php';
require_once './src/Model/Common/Security.php';

class AdminMissionController
{
    private $Mission;
    private $Agent;
    private $Country;
    private $Cible;
    private $Contact;
    private $Planque;
    private $Status;
    private $Type;
    private $Speciality;
    private $Security;

    public function __construct()
    {
        $this->Mission = new Mission();
        $this->Agent = new Agent();
        $this->Country = new Country();
        $this->Cible = new Cible();
        $this->Contact = new Contact();
        $this->Planque = new Planque();
        $this->Status = new Status();
        $this->Type = new Type();
        $this->Speciality = new Speciality();
        $this->Security = new Security();
    }

    public function adminMissionPage()
    // Accueil admin de la section mission
    {

        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère  la pagination
        (isset($_GET['page']) and !empty($_GET['page'])) ? $page = max(1, $this->Security->filter_form($_GET['page'])) : $page = 1;

        // Nombre d'éléments par page
        $itemsPerPage = 10;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset($_GET['search']) and !empty($_GET['search']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) {
            $missions = $this->Mission->getSearchMissionNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $missions = $this->Mission->getPaginationAllMissionNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Mission->getAllMissions())) {
            $pageMax = ceil(count($this->Mission->getAllMissions()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // Récupère le json des éléments pour traitement dans le form add
        $listAgents = json_encode($this->Agent->getAllAgentNames(), JSON_HEX_QUOT);
        $listCountries = json_encode($this->Country->getAllCountryNames(), JSON_HEX_QUOT);
        $countries = $this->Country->getAllCountryNames();
        $listCibles = json_encode($this->Cible->getAllCibleNames(), JSON_HEX_QUOT);
        $listContacts = json_encode($this->Contact->getAllContactNames(), JSON_HEX_QUOT);
        $listPlanques = json_encode($this->Planque->getAllPlanqueNames(), JSON_HEX_QUOT);
        $listStatus = json_encode($this->Status->getAllStatusNames(), JSON_HEX_QUOT);
        $status = $this->Status->getAllStatusNames();
        $listTypes = json_encode($this->Type->getAllTypeNames(), JSON_HEX_QUOT);
        $types = $this->Type->getAllTypeNames();
        $listSpeciality = json_encode($this->Speciality->getAllSpecialityNames(), JSON_HEX_QUOT);
        $speciality = $this->Speciality->getAllSpecialityNames();
        $cibles = $this->Cible->getAllCibleNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'missions',
            'elements' => $missions,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'listAgents' => $listAgents,
            'listCountries' => $listCountries,
            'countries' => $countries,
            'status' => $status,
            'spe' => $speciality,
            'type' => $types,
            'cibles' => $cibles,
            'listCibles' => $listCibles,
            'listContacts' => $listContacts,
            'listPlanques' => $listPlanques,
            'listStatus' => $listStatus,
            'listTypes' => $listTypes,
            'listSpeciality' => $listSpeciality,
            'deleteUrl' => '/admin/manage-mission/delete',
            'addUrl' => '/admin/manage-mission/add',
            'updateUrl' => '/admin/manage-mission/update',
            'previousUrl' => '/admin/manage-mission',
            'token' => $token
        ]);
    }
    public function adminSuccessActionMission()
    // Résultat succès ou echec après action sur mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        $res = null;
        $idElement = null;
        $missions = null;

        // On récupère le résultat de la requête
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset($_SESSION['idElement']) and !empty($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page mission
            header('Location: ' . BASE_URL . '/admin/manage-mission');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'missions',
            'addResult' => $res,
            'deleteUrl' => '/admin/manage-mission/delete',
            'addUrl' => '/admin/manage-mission/add',
            'updateUrl' => '/admin/manage-mission/update',
            'previousUrl' => '/admin/manage-mission'
        ]);

    }

    public function adminAddMission()
    // Ajout de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {

            // le nom de code
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $missionName = $this->Security->filter_form($_POST['addElementName']) : $missionName = '';

            // le titre
            (isset($_POST['addElementTitle']) and !empty($_POST['addElementTitle'])) ? $missionTitle = $this->Security->filter_form($_POST['addElementTitle']) : $missionTitle = '';

            // la description
            (isset($_POST['addElementDesc']) and !empty($_POST['addElementDesc'])) ? $missionDesc = $this->Security->filter_form($_POST['addElementDesc']) : $missionDesc = '';

            // la date de début
            (isset($_POST['updateElementBeginDate']) and !empty($_POST['updateElementBeginDate'])) ? $missionBeginDate = $this->Security->filter_form($_POST['updateElementBeginDate']) : $missionBeginDate = '';

            // la date de fin
            (isset($_POST['updateElementEndDate']) and !empty($_POST['updateElementEndDate'])) ? $missionEndDate = $this->Security->filter_form($_POST['updateElementEndDate']) : $missionEndDate = '';

            // le pays
            (isset($_POST['addElementCountry']) and !empty($_POST['addElementCountry'])) ? $missionCountry = $this->Security->filter_form($_POST['addElementCountry']) : $missionCountry = '';

            // le status
            (isset($_POST['addElementStatus']) and !empty($_POST['addElementStatus'])) ? $missionStatus = $this->Security->filter_form($_POST['addElementStatus']) : $missionStatus = '';

            // le type
            (isset($_POST['addElementType']) and !empty($_POST['addElementType'])) ? $missionType = $this->Security->filter_form($_POST['addElementType']) : $missionType = '';

            // la spécialité
            (isset($_POST['addElementSpe']) and !empty($_POST['addElementSpe'])) ? $missionSpe = $this->Security->filter_form($_POST['addElementSpe']) : $missionSpe = '';

            // la planque
            (isset($_POST['addElementPlanque']) and !empty($_POST['addElementPlanque'])) ? $missionPlanque = $this->Security->filter_form($_POST['addElementPlanque']) : $missionPlanque = '';

            // la liste des contacts
            (isset($_POST['addContacts']) and !empty($_POST['addContacts'])) ? $missionContact = $this->Security->filter_form_array($_POST['addContacts']) : $missionContact = '';

            // la liste des cibles
            (isset($_POST['cibles']) and !empty($_POST['cibles'])) ? $missionCibles = $this->Security->filter_form_array($_POST['cibles']) : $missionCibles = '';

            // la liste des agents
            (isset($_POST['addAgent']) and !empty($_POST['addAgent'])) ? $missionAgents = $this->Security->filter_form_array($_POST['addAgent']) : $missionAgents = '';


            if (!empty($missionName) && !empty($missionTitle) && !empty($missionBeginDate) && !empty($missionEndDate) && !empty($missionCountry) && !empty($missionStatus) && !empty($missionType) && !empty($missionSpe) && !empty($missionPlanque) && !empty($missionContact) && !empty($missionCibles) && !empty($missionAgents) && !empty($missionDesc) && $this->Mission->verifyMissionConstraints($missionCountry, $missionSpe, $missionCibles, $missionContact, $missionAgents, $missionPlanque)) {
                // Si les variables ne sont pas vides et les conditions sont respectées
                // on fait l'ajout en BDD et on récupère le résultat
                $res = $this->Mission->addMission($missionTitle, $missionName, $missionDesc, $missionBeginDate, $missionEndDate, $missionCountry, $missionType, $missionStatus, $missionSpe, $missionCibles, $missionContact, $missionAgents, $missionPlanque);
                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;
            } else {
                // on indique qu'il y a une erreur
                $res = 'une erreur est survenue';

                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;


            }
        }
                // on regénère le token
                $this->Security->regenerateToken();

                header('Location: ' . BASE_URL . '/admin/manage-mission/action/success');
                exit;

    }

    public function adminDeleteMission()
    // Suppression de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id mission à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $missionAction = $this->Security->filter_form($_POST['deleteElementId']) : $missionAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Mission->deleteMission($missionAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $missionAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-mission/action/success');
        exit;


    }

    public function adminUpdateMissionPage()
    // Page permettant la saisie pour la modification de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du mission à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $missionAction = $this->Security->filter_form($_GET['UpdateElementId']) : $missionAction = '';

        // Récupère l'e 'mission à modifier
        $mission = $this->Mission->getMission($missionAction);
        $modifySection = true;

        // on regénère le token
        $this->Security->regenerateToken();

        // On récupère le token pour le nouveau form
        $token = $this->Security->getToken();
        
        // Récupère le json des éléments pour traitement dans le form add
        $listAgents = json_encode($this->Agent->getAllAgentNames(), JSON_HEX_QUOT);
        $listCountries = json_encode($this->Country->getAllCountryNames(), JSON_HEX_QUOT);
        $countries = $this->Country->getAllCountryNames();
        $listCibles = json_encode($this->Cible->getAllCibleNames(), JSON_HEX_QUOT);
        $listContacts = json_encode($this->Contact->getAllContactNames(), JSON_HEX_QUOT);
        $listPlanques = json_encode($this->Planque->getAllPlanqueNames(), JSON_HEX_QUOT);
        $listStatus = json_encode($this->Status->getAllStatusNames(), JSON_HEX_QUOT);
        $status = $this->Status->getAllStatusNames();
        $listTypes = json_encode($this->Type->getAllTypeNames(), JSON_HEX_QUOT);
        $types = $this->Type->getAllTypeNames();
        $listSpeciality = json_encode($this->Speciality->getAllSpecialityNames(), JSON_HEX_QUOT);
        $speciality = $this->Speciality->getAllSpecialityNames();
        $cibles = $this->Cible->getAllCibleNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'missions',
            'elements' => $mission,
            'modifySection' => $modifySection,
            'deleteUrl' => '/admin/manage-mission/delete',
            'updateUrl' => '/admin/manage-mission/update',
            'previousUrl' => '/admin/manage-mission',
            'listAgents' => $listAgents,
            'listCountries' => $listCountries,
            'countries' => $countries,
            'status' => $status,
            'spe' => $speciality,
            'type' => $types,
            'cibles' => $cibles,
            'listCibles' => $listCibles,
            'listContacts' => $listContacts,
            'listPlanques' => $listPlanques,
            'listStatus' => $listStatus,
            'listTypes' => $listTypes,
            'listSpeciality' => $listSpeciality,
            'token' => $token
        ]);

    }

    public function adminUpdateMission()
    // Modification de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

         // on récupère la planque ajoutée et le token
         if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {

            // l'id'
            (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $missionId = $this->Security->filter_form($_POST['updateElementId']) : $missionId = '';

            // le nom de code
            (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $missionName = $this->Security->filter_form($_POST['addElementName']) : $missionName = '';

            // le titre
            (isset($_POST['addElementTitle']) and !empty($_POST['addElementTitle'])) ? $missionTitle = $this->Security->filter_form($_POST['addElementTitle']) : $missionTitle = '';

            // la description
            (isset($_POST['addElementDesc']) and !empty($_POST['addElementDesc'])) ? $missionDesc = $this->Security->filter_form($_POST['addElementDesc']) : $missionDesc = '';

            // la date de début
            (isset($_POST['updateElementBeginDate']) and !empty($_POST['updateElementBeginDate'])) ? $missionBeginDate = $this->Security->filter_form($_POST['updateElementBeginDate']) : $missionBeginDate = '';

            // la date de fin
            (isset($_POST['updateElementEndDate']) and !empty($_POST['updateElementEndDate'])) ? $missionEndDate = $this->Security->filter_form($_POST['updateElementEndDate']) : $missionEndDate = '';

            // le pays
            (isset($_POST['addElementCountry']) and !empty($_POST['addElementCountry'])) ? $missionCountry = $this->Security->filter_form($_POST['addElementCountry']) : $missionCountry = '';

            // le status
            (isset($_POST['addElementStatus']) and !empty($_POST['addElementStatus'])) ? $missionStatus = $this->Security->filter_form($_POST['addElementStatus']) : $missionStatus = '';

            // le type
            (isset($_POST['addElementType']) and !empty($_POST['addElementType'])) ? $missionType = $this->Security->filter_form($_POST['addElementType']) : $missionType = '';

            // la spécialité
            (isset($_POST['addElementSpe']) and !empty($_POST['addElementSpe'])) ? $missionSpe = $this->Security->filter_form($_POST['addElementSpe']) : $missionSpe = '';

            // la planque
            (isset($_POST['addElementPlanque']) and !empty($_POST['addElementPlanque'])) ? $missionPlanque = $this->Security->filter_form($_POST['addElementPlanque']) : $missionPlanque = '';

            // la liste des contacts
            (isset($_POST['addContacts']) and !empty($_POST['addContacts'])) ? $missionContact = $this->Security->filter_form_array($_POST['addContacts']) : $missionContact = '';

            // la liste des cibles
            (isset($_POST['cibles']) and !empty($_POST['cibles'])) ? $missionCibles = $this->Security->filter_form_array($_POST['cibles']) : $missionCibles = '';

            // la liste des agents
            (isset($_POST['addAgent']) and !empty($_POST['addAgent'])) ? $missionAgents = $this->Security->filter_form_array($_POST['addAgent']) : $missionAgents = '';


            if (!empty($missionCountry) && !empty($missionStatus) && !empty($missionId) && !empty($missionType) && !empty($missionSpe) && !empty($missionPlanque) && !empty($missionContact) && !empty($missionCibles) && !empty($missionAgents) && $this->Mission->verifyMissionConstraints($missionCountry, $missionSpe, $missionCibles, $missionContact, $missionAgents, $missionPlanque)) {
                // Si les variables ne sont pas vides et les conditions sont respectées
                // on fait l'ajout en BDD et on récupère le résultat
                $res = $this->Mission->updateMission($missionId, $missionTitle, $missionName, $missionDesc, $missionBeginDate, $missionEndDate, $missionCountry, $missionType, $missionStatus, $missionSpe, $missionCibles, $missionContact, $missionAgents, $missionPlanque);
                
                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;
            } else {
                // on indique qu'il y a une erreur
                $res = 'une erreur est survenueeeeee';

                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;
            }
        }
        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-mission/action/success');
        exit;
    }

}
