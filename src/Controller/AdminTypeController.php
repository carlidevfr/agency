<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Type.php';
require_once './src/Model/Common/Security.php';

class AdminTypeController
{
    private $Missions;
    private $Type;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Type = new Type();
        $this->Security = new Security();

    }

    public function adminTypePage()
    // Accueil admin de la section type
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
            $type = $this->Type->getSearchTypeNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();
        } else {
            $type = $this->Type->getPaginationAllTypeNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus. Dans un if pour éviter toute erreur
        if (!empty($this->Type->getAllTypeNames())) {
            $pageMax = ceil(count($this->Type->getAllTypeNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'type',
            'elements' => $type,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => '/admin/manage-type/delete',
            'addUrl' => '/admin/manage-type/add',
            'updateUrl' => '/admin/manage-type/update',
            'previousUrl' => '/admin/manage-type',
            'token' => $token
        ]);
    }
    public function adminSuccessActionType()
    // Résultat succès ou echec après action sur type
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

            // On récupère la liste des missions contenant l'id que l'on a tenté de supprimer, car peut empêcher la suppression
            (isset($idElement) and !empty($idElement) ? $missions = $this->Missions->getSelectedMissions('', '', $idElement, '', '') : $missions = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page type
            header('Location: ' . BASE_URL . '/admin/manage-type');
        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'type',
            'addResult' => $res,
            'missions' => $missions,
            'deleteUrl' => '/admin/manage-type/delete',
            'addUrl' => '/admin/manage-type/add',
            'updateUrl' => '/admin/manage-type/update',
            'previousUrl' => '/admin/manage-type'
        ]);

    }

    public function adminAddType()
    // Ajout de type
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le type ajouté et le token
        (isset($_POST['addElementName']) and !empty($_POST['addElementName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $typeAction = $this->Security->filter_form($_POST['addElementName']) : $typeAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Type->addType($typeAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-type/action/success');
        exit;

    }

    public function adminDeleteType()
    // Suppression de type
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id type à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $typeAction = $this->Security->filter_form($_POST['deleteElementId']) : $typeAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Type->deleteType($typeAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $typeAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-type/action/success');
        exit;


    }

    public function adminUpdateTypePage()
    // Page permettant la saisie pour la modification de type
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du type à modifier et vérifie si la requête est authentifiée
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $typeAction = $this->Security->filter_form($_GET['UpdateElementId']) : $typeAction = '';

        // Récupère le type à modifier
        $type = $this->Type->getBytypeId($typeAction);
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
            'pageName' => 'type',
            'elements' => $type,
            'modifySection' => $modifySection,
            'deleteUrl' => '/admin/manage-type/delete',
            'addUrl' => '/admin/manage-type/add',
            'updateUrl' => '/admin/manage-type/update',
            'previousUrl' => '/admin/manage-type',
            'token' => $token
        ]);

    }

    public function adminUpdateType()
    // Modification de type
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id type à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $typeAction = $this->Security->filter_form($_POST['updateElementId']) : $typeAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset($_POST['updatedName']) and !empty($_POST['updatedName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Type->updateType($typeAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-type/action/success');
        exit;
    }

}
