<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Status.php';
require_once './src/Model/Common/Security.php';

class AdminStatusController
{
    private $Missions;
    private $Status;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Status = new Status();
        $this->Security = new Security();

    }

    public function adminStatusPage()
    // Accueil admin de la section status
    {
        //Récupère  la pagination
        (isset($_GET['page']) and !empty($_GET['page'])) ? $page = max(1, $this->Security->filter_form($_GET['page'])) : $page = 1;

        // Nombre d'éléments par page
        $itemsPerPage = 10;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset($_GET['search']) and !empty($_GET['search'])) {
            $status = $this->Status->getSearchStatusNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);
        } else {
            $status = $this->Status->getPaginationAllStatusNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        $pageMax = ceil(count($this->Status->getAllStatusNames()) / $itemsPerPage);

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName'=> 'statut',
            'elements' => $status,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl'=> '/admin/manage-status/delete',
            'addUrl'=> '/admin/manage-status/add',
            'updateUrl'=> '/admin/manage-status/update',
            'previousUrl' => '/admin/manage-status'
        ]);
    }
    public function adminSuccessActionStatus()
    // Résultat succès ou echec après action sur status
    {
        $res = null;
        $idElement = null;
        $missions = null;

        // On récupère le résultat de la requête
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset($_SESSION['idElement']) and !empty($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

            // On récupère la liste des missions contenant l'id que l'on a tenté de supprimer, car peut empêcher la suppression
            (isset($idElement) and !empty($idElement) ? $missions = $this->Missions->getSelectedMissions('','',$idElement,'','') : $missions = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page status
            header('Location: ' . BASE_URL . '/admin/manage-status');
        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName'=> 'statut',
            'addResult' => $res,
            'missions' => $missions,
            'deleteUrl'=> '/admin/manage-status/delete',
            'addUrl'=> '/admin/manage-status/add',
            'updateUrl'=> '/admin/manage-status/update',
            'previousUrl' => '/admin/manage-status'
        ]);

    }

    public function adminAddStatus()
    // Ajout de status
    {
        // on récupère le status ajouté
        (isset($_POST['addElementName']) and !empty($_POST['addElementName'])) ? $statusAction = $this->Security->filter_form($_POST['addElementName']) : $statusAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Status->addStatus($statusAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        header('Location: ' . BASE_URL . '/admin/manage-status/action/success');
        exit;

    }

    public function adminDeleteStatus()
    // Suppression de status
    {
        // on récupère l'id status à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId'])) ? $statusAction = $this->Security->filter_form($_POST['deleteElementId']) : $statusAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Status->deleteStatus($statusAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $statusAction;

        header('Location: ' . BASE_URL . '/admin/manage-status/action/success');
        exit;


    }

    public function adminUpdateStatusPage()
    // Page permettant la saisie pour la modification de status
    {
        //Récupère l'id du status à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId'])) ? $statusAction = $this->Security->filter_form($_GET['UpdateElementId']) : $statusAction = '';

        // Récupère le status à modifier
        $status = $this->Status->getBystatusId($statusAction);
        $modifySection = true;
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName'=> 'status',
            'elements' => $status,
            'modifySection' => $modifySection,
            'deleteUrl'=> '/admin/manage-status/delete',
            'addUrl'=> '/admin/manage-status/add',
            'updateUrl'=> '/admin/manage-status/update',
            'previousUrl' => '/admin/manage-status'
        ]);

    }

    public function adminUpdateStatus()
    // Modification de status
    {
        // on récupère l'id status à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $statusAction = $this->Security->filter_form($_POST['updateElementId']) : $statusAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset($_POST['updatedName']) and !empty($_POST['updatedName'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Status->updateStatus($statusAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        header('Location: ' . BASE_URL . '/admin/manage-status/action/success');
        exit;


    }

}
