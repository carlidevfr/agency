<?php
use PHPUnit\Framework\Constraint\IsEmpty;

require_once './src/Model/Mission.php';
require_once './src/Model/Agent.php';
require_once './src/Model/Speciality.php';
require_once './src/Model/Common/Security.php';

class AdminAgentController
{
    private $Missions;
    private $Agent;
    private $Security;

    private $speciality;


    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Agent = new Agent();
        $this->Security = new Security();
        $this->speciality = new Speciality();
    }

    public function adminAgentPage()
    // Accueil admin de la section agent
    {

        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère  la pagination
        (isset ($_GET['page']) and !empty ($_GET['page'])) ? $page = max(1, $this->Security->filter_form($_GET['page'])) : $page = 1;

        // Nombre d'éléments par page
        $itemsPerPage = 10;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset ($_GET['search']) and !empty ($_GET['search']) and isset ($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) {
            $agents = $this->Agent->getSearchAgentNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $agents = $this->Agent->getPaginationAllAgentNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty ($this->Agent->getAllAgentNames())) {
            $pageMax = ceil(count($this->Agent->getAllAgentNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // On récupère la liste des personnes qui ne sont pas des agents pour un éventuel add
        $cibles = $this->Agent->getNotAgentNames();

        // On récupère la liste des spécialités pour un éventuel add
        $spe = $this->speciality->getAllSpecialityNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'agents',
            'elements' => $agents,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'cibles' => $cibles,
            'spe' => $spe,
            'deleteUrl' => '/admin/manage-agent/delete',
            'addUrl' => '/admin/manage-agent/add',
            'updateUrl' => '/admin/manage-agent/update',
            'previousUrl' => '/admin/manage-agent',
            'token' => $token
        ]);
    }
    public function adminSuccessActionAgent()
    // Résultat succès ou echec après action sur agent
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        $res = null;
        $idElement = null;
        $missions = null;

        // On récupère le résultat de la requête
        if (isset ($_SESSION['resultat']) and !empty ($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset ($_SESSION['idElement']) and !empty ($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

            // On récupère la liste des éléments liés pouvant empêcher la suppression
            (isset ($idElement) and !empty ($idElement) ? $data = $this->Agent->getRelatedAgent($idElement) : $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page agent
            header('Location: ' . BASE_URL . '/admin/manage-agent');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'agents',
            'addResult' => $res,
            'data' => $data,
            'deleteUrl' => '/admin/manage-agent/delete',
            'addUrl' => '/admin/manage-agent/add',
            'updateUrl' => '/admin/manage-agent/update',
            'previousUrl' => '/admin/manage-agent'
        ]);

    }

    public function adminAddAgent()
    // Ajout de agent
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // l id
            (isset ($_POST['addElementId']) and !empty ($_POST['addElementId'])) ? $agentName = $this->Security->filter_form($_POST['addElementId']) : $agentName = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Agent->addAgent($agentName);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-agent/action/success');
        exit;
    }

    public function adminDeleteAgent()
    // Suppression de agent
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id agent à supprimer
        (isset ($_POST['deleteElementId']) and !empty ($_POST['deleteElementId']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $agentAction = $this->Security->filter_form($_POST['deleteElementId']) : $agentAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Agent->deleteAgent($agentAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $agentAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-agent/action/success');
        exit;


    }


}
