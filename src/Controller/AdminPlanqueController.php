<?php
require_once './src/Model/Planque.php';
require_once './src/Model/Country.php';
require_once './src/Model/Mission.php';
require_once './src/Model/Common/Security.php';

class AdminPlanqueController
{
    private $Planque;
    private $Security;
    private $Country;
    private $Mission;



    public function __construct()
    {
        $this->Planque = new Planque();
        $this->Security = new Security();
        $this->Country = new Country();
        $this->Mission = new Mission();
    }

    public function adminPlanquePage()
    // Accueil admin de la section planque
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
        if (isset ($_GET['search']) and !empty ($_GET['search'] and isset ($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok']))) {
            $planque = $this->Planque->getSearchPlanqueNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $planque = $this->Planque->getPaginationAllPlanqueNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus. Dans un if pour éviter toute erreur
        if (!empty ($this->Planque->getAllPlanqueNames())) {
            $pageMax = ceil(count($this->Planque->getAllPlanqueNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // On récupère la liste des pays pour un éventuel add
        $countries = $this->Country->getAllCountryNames();

        // On récupère la liste des missions pour un éventuel add
        $missions = $this->Mission->getAllMissions();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'planques',
            'elements' => $planque,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'countries' => $countries,
            'missions' => $missions,
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
        if (isset ($_SESSION['resultat']) and !empty ($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Si l'id est en variable session on le récupère
            (isset ($_SESSION['idElement']) and !empty ($_SESSION['idElement'])) ? $idElement = $this->Security->filter_form($_SESSION['idElement']) : $idElement = '';

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
            'pageName' => 'planques',
            'addResult' => $res,
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

        // on récupère la planque ajoutée et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // le nom
            (isset ($_POST['addElementName']) and !empty ($_POST['addElementName'])) ? $planqueName = $this->Security->filter_form($_POST['addElementName']) : $planqueName = '';

            //l'adresse
            (isset ($_POST['addElementAdress']) and !empty ($_POST['addElementAdress'])) ? $planqueAdress = $this->Security->filter_form($_POST['addElementAdress']) : $planqueAdress = '';

            //le pays
            (isset ($_POST['addElementCountry']) and !empty ($_POST['addElementCountry'])) ? $planqueCountry = $this->Security->filter_form($_POST['addElementCountry']) : $planqueCountry = '';

            //la mission
            (isset ($_POST['addElementMission']) and !empty ($_POST['addElementMission'])) ? $planqueMission = $this->Security->filter_form($_POST['addElementMission']) : $planqueMission = null;

            //le type
            (isset ($_POST['addElementType']) and !empty ($_POST['addElementType'])) ? $planqueType = $this->Security->filter_form($_POST['addElementType']) : $planqueType = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Planque->addPlanque($planqueName, $planqueAdress, $planqueCountry, $planqueMission, $planqueType);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }



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
        (isset ($_POST['deleteElementId']) and !empty ($_POST['deleteElementId']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $planqueAction = $this->Security->filter_form($_POST['deleteElementId']) : $planqueAction = '';

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
        (isset ($_GET['UpdateElementId']) and !empty ($_GET['UpdateElementId']) and isset ($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $planqueAction = $this->Security->filter_form($_GET['UpdateElementId']) : $planqueAction = '';

        // Récupère la planque à modifier
        $planque = $this->Planque->getByplanqueId($planqueAction);
        $modifySection = true;

        // on regénère le token
        $this->Security->regenerateToken();

        // On récupère le token pour le nouveau form
        $token = $this->Security->getToken();

        // On récupère la liste des pays pour un éventuel add
        $countries = $this->Country->getAllCountryNames();


        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'planques',
            'elements' => $planque,
            'countries' => $countries,
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


        // on récupère la planque ajoutée et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // l'id
            (isset ($_POST['updateElementId']) and !empty ($_POST['updateElementId'])) ? $planqueId = $this->Security->filter_form($_POST['updateElementId']) : $planqueId = '';

            // le nom
            (isset ($_POST['updatedName']) and !empty ($_POST['updatedName'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

            //l'adresse
            (isset ($_POST['updatedElementAdress']) and !empty ($_POST['updatedElementAdress'])) ? $newAdress = $this->Security->filter_form($_POST['updatedElementAdress']) : $newAdress = '';

            //le pays
            (isset ($_POST['updatedElementCountry']) and !empty ($_POST['updatedElementCountry'])) ? $newCountry = $this->Security->filter_form($_POST['updatedElementCountry']) : $newCountry = '';

            //la mission
            (isset ($_POST['updatedElementMission']) and !empty ($_POST['updatedElementMission'])) ? $newMission = $this->Security->filter_form($_POST['updatedElementMission']) : $newMission = null;

            //le type
            (isset ($_POST['updatedElementType']) and !empty ($_POST['updatedElementType'])) ? $newType = $this->Security->filter_form($_POST['updatedElementType']) : $newType = '';

            // on fait la modification en BDD et on récupère le résultat
            $res = $this->Planque->updatePlanque($planqueId, $newName, $newAdress, $newCountry, $newMission, $newType);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-planque/action/success');
        exit;
    }

}
