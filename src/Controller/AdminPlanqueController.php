<?php
require_once './src/Model/Planque.php';
require_once './src/Model/Common/Security.php';

class AdminPlanqueController
{
    private $Missions;
    private $Planque;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Planque = new Planque();
        $this->Security = new Security();

    }

    public function adminPlanquePage()
    // Accueil admin de la section planque
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
        if (isset($_GET['search']) and !empty($_GET['search'] and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok']))) {
            $planque = $this->Planque->getSearchPlanqueNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();
        } else {
            $planque = $this->Planque->getPaginationAllPlanqueNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus. Dans un if pour éviter toute erreur
        if (!empty($this->Planque->getAllPlanqueNames())) {
            $pageMax = ceil(count($this->Planque->getAllPlanqueNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'planque',
            'elements' => $planque,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => '/admin/manage-planque/delete',
            'addUrl' => '/admin/manage-planque/add',
            'updateUrl' => '/admin/manage-planque/update',
            'previousUrl' => '/admin/manage-planque',
            'token' => $token
        ]);
    }
    public function adminSuccessActionPlanque()
    // Résultat succès ou echec après action sur planque
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        $res = null;
        $idElement = null;

        // On récupère le résultat de la requête
        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset($_SESSION['idElement']) and !empty($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

           // On récupère la liste des éléments liés pouvant empêcher la suppression
           (isset($idElement) and !empty($idElement) ? $data = $this->Planque->getRelatedPlanque($idElement): $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page planque
            header('Location: ' . BASE_URL . '/admin/manage-planque');
        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'planque',
            'addResult' => $res,
            'data' => $data,
            'deleteUrl' => '/admin/manage-planque/delete',
            'addUrl' => '/admin/manage-planque/add',
            'updateUrl' => '/admin/manage-planque/update',
            'previousUrl' => '/admin/manage-planque'
        ]);

    }

    public function adminAddPlanque()
    // Ajout de planque
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le planque ajouté et le token
        (isset($_POST['addElementName']) and !empty($_POST['addElementName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $planqueAction = $this->Security->filter_form($_POST['addElementName']) : $planqueAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Planque->addPlanque($planqueAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-planque/action/success');
        exit;

    }

    public function adminDeletePlanque()
    // Suppression de planque
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id planque à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $planqueAction = $this->Security->filter_form($_POST['deleteElementId']) : $planqueAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Planque->deletePlanque($planqueAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $planqueAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-planque/action/success');
        exit;


    }

    public function adminUpdatePlanquePage()
    // Page permettant la saisie pour la modification de planque
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du planque à modifier et vérifie si la requête est authentifiée
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $planqueAction = $this->Security->filter_form($_GET['UpdateElementId']) : $planqueAction = '';

        // Récupère le planque à modifier
        $planque = $this->Planque->getByplanqueId($planqueAction);
        $modifySection = true;

        // on regénère le token
        $this->Security->regenerateToken();

        // On récupère le token pour le nouveau form
        $token = $this->Security->getToken();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'planque',
            'elements' => $planque,
            'modifySection' => $modifySection,
            'deleteUrl' => '/admin/manage-planque/delete',
            'addUrl' => '/admin/manage-planque/add',
            'updateUrl' => '/admin/manage-planque/update',
            'previousUrl' => '/admin/manage-planque',
            'token' => $token
        ]);

    }

    public function adminUpdatePlanque()
    // Modification de planque
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id planque à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $planqueAction = $this->Security->filter_form($_POST['updateElementId']) : $planqueAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset($_POST['updatedName']) and !empty($_POST['updatedName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Planque->updatePlanque($planqueAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-planque/action/success');
        exit;
    }

}
