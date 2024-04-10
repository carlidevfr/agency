<?php
use PHPUnit\Framework\Constraint\IsEmpty;
require_once './src/Model/Mission.php';
require_once './src/Model/Country.php';
require_once './src/Model/Common/Security.php';

class AdminCountryController
{
    private $Missions;
    private $Country;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Country = new Country();
        $this->Security = new Security();

    }

    public function adminCountryPage()
    // Accueil admin de la section country
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
            $countries = $this->Country->getSearchCountryNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
            
        } else {
            $countries = $this->Country->getPaginationAllCountryNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Country->getAllCountryNames())) {
            $pageMax = ceil(count($this->Country->getAllCountryNames()) / $itemsPerPage);
        }else{
            $pageMax = 1;
        }
        
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'pays',
            'elements' => $countries,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-country/delete',
            'addUrl' => 'admin/manage-country/add',
            'updateUrl' => 'admin/manage-country/update',
            'previousUrl' => 'admin/manage-country',
            'token' => $token
        ]);
    }
    public function adminSuccessActionCountry()
    // Résultat succès ou echec après action sur pays
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

            // On récupère la liste des éléments liés pouvant empêcher la suppression
            (isset($idElement) and !empty($idElement) ? $data = $this->Country->getRelatedCountries($idElement): $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page pays
            header('Location: ' . BASE_URL . 'admin/manage-country');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'pays',
            'addResult' => $res,
            'data' => $data,
            'deleteUrl' => 'admin/manage-country/delete',
            'addUrl' => 'admin/manage-country/add',
            'updateUrl' => 'admin/manage-country/update',
            'previousUrl' => 'admin/manage-country'
        ]);

    }

    public function adminAddCountry()
    // Ajout de pays
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le pays ajouté
        (isset($_POST['addElementName']) and !empty($_POST['addElementName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $countryAction = $this->Security->filter_form($_POST['addElementName']) : $countryAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Country->addCountry($countryAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-country/action/success');
        exit;


    }

    public function adminDeleteCountry()
    // Suppression de pays
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id pays à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $countryAction = $this->Security->filter_form($_POST['deleteElementId']) : $countryAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Country->deleteCountry($countryAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $countryAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-country/action/success');
        exit;


    }

    public function adminUpdateCountryPage()
    // Page permettant la saisie pour la modification de pays
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du pays à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $countryAction = $this->Security->filter_form($_GET['UpdateElementId']) : $countryAction = '';

        // Récupère le pays à modifier
        $country = $this->Country->getByCountryId($countryAction);
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
            'pageName' => 'pays',
            'elements' => $country,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-country/delete',
            'addUrl' => 'admin/manage-country/add',
            'updateUrl' => 'admin/manage-country/update',
            'previousUrl' => 'admin/manage-country',
            'token' => $token
        ]);

    }

    public function adminUpdateCountry()
    // Modification de pays
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id pays à Modifier
        (isset($_POST['updateElementId']) and !empty($_POST['updateElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $countryAction = $this->Security->filter_form($_POST['updateElementId']) : $countryAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset($_POST['updatedName']) and !empty($_POST['updatedName']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Country->updateCountry($countryAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-country/action/success');
        exit;


    }

}
