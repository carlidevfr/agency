<?php
use PHPUnit\Framework\Constraint\IsEmpty;

require_once './src/Model/Mission.php';
require_once './src/Model/Cible.php';
require_once './src/Model/Country.php';
require_once './src/Model/Common/Security.php';

class AdminCibleController
{
    private $Missions;
    private $Cible;
    private $Country;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Cible = new Cible();
        $this->Security = new Security();
        $this->Country = new Country();
    }

    public function adminCiblePage()
    // Accueil admin de la section cible
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
            $cibles = $this->Cible->getSearchCibleNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $cibles = $this->Cible->getPaginationAllCibleNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty ($this->Cible->getAllCibleNames())) {
            $pageMax = ceil(count($this->Cible->getAllCibleNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // On récupère la liste des pays pour un éventuel add
        $countries = $this->Country->getAllCountryNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'cibles',
            'elements' => $cibles,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'countries' => $countries,
            'deleteUrl' => '/admin/manage-cible/delete',
            'addUrl' => '/admin/manage-cible/add',
            'updateUrl' => '/admin/manage-cible/update',
            'previousUrl' => '/admin/manage-cible',
            'token' => $token
        ]);
    }
    public function adminSuccessActionCible()
    // Résultat succès ou echec après action sur cible
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
            (isset ($idElement) and !empty ($idElement) ? $data = $this->Cible->getRelatedCible($idElement) : $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page cible
            header('Location: ' . BASE_URL . '/admin/manage-cible');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'cibles',
            'addResult' => $res,
            'data' => $data,
            'deleteUrl' => '/admin/manage-cible/delete',
            'addUrl' => '/admin/manage-cible/add',
            'updateUrl' => '/admin/manage-cible/update',
            'previousUrl' => '/admin/manage-cible'
        ]);

    }

    public function adminAddCible()
    // Ajout de cible
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // le nom de code
            (isset ($_POST['addElementName']) and !empty ($_POST['addElementName'])) ? $cibleName = $this->Security->filter_form($_POST['addElementName']) : $cibleName = '';

            //le prénom
            (isset ($_POST['addElementFirstName']) and !empty ($_POST['addElementFirstName'])) ? $cibleFirstName = $this->Security->filter_form($_POST['addElementFirstName']) : $cibleFirstName = '';

            //le nom
            (isset ($_POST['addElementLastName']) and !empty ($_POST['addElementLastName'])) ? $cibleLastName = $this->Security->filter_form($_POST['addElementLastName']) : $cibleLastName = '';

            //la date de naissance
            (isset ($_POST['addElementBirthDate']) and !empty ($_POST['addElementBirthDate'])) ? $cibleBirthDate = $this->Security->filter_form($_POST['addElementBirthDate']) : $cibleBirthDate = null;

            //le pays
            (isset ($_POST['addElementCountry']) and !empty ($_POST['addElementCountry'])) ? $cibleCountry = $this->Security->filter_form($_POST['addElementCountry']) : $cibleCountry = '';
           
            //cible active ?
            (isset ($_POST['addElementActive']) and $_POST['addElementActive'] === '1') ? $cibleActive = $this->Security->filter_form($_POST['addElementActive']) : $cibleActive = '0';
            
            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Cible->addCible($cibleName, $cibleFirstName, $cibleLastName, $cibleBirthDate, $cibleCountry, $cibleActive);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-cible/action/success');
        exit;
    }

    public function adminDeleteCible()
    // Suppression de cible
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id cible à supprimer
        (isset ($_POST['deleteElementId']) and !empty ($_POST['deleteElementId']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $cibleAction = $this->Security->filter_form($_POST['deleteElementId']) : $cibleAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Cible->deleteCible($cibleAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $cibleAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-cible/action/success');
        exit;


    }

    public function adminUpdateCiblePage()
    // Page permettant la saisie pour la modification de cible
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du cible à modifier
        (isset ($_GET['UpdateElementId']) and !empty ($_GET['UpdateElementId']) and isset ($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $cibleAction = $this->Security->filter_form($_GET['UpdateElementId']) : $cibleAction = '';

        // Récupère le cible à modifier
        $cible = $this->Cible->getByCibleId($cibleAction);
        $modifySection = true;

        // on regénère le token
        $this->Security->regenerateToken();

        // On récupère le token pour le nouveau form
        $token = $this->Security->getToken();

        // On récupère la liste des pays
        $countries = $this->Country->getAllCountryNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'cibles',
            'elements' => $cible,
            'countries' => $countries,
            'modifySection' => $modifySection,
            'deleteUrl' => '/admin/manage-cible/delete',
            'addUrl' => '/admin/manage-cible/add',
            'updateUrl' => '/admin/manage-cible/update',
            'previousUrl' => '/admin/manage-cible',
            'token' => $token
        ]);

    }

    public function adminUpdateCible()
    // Modification de cible
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // l'id
            (isset ($_POST['updateElementId']) and !empty ($_POST['updateElementId'])) ? $cibleId = $this->Security->filter_form($_POST['updateElementId']) : $cibleId = '';

            // le nom de code
            (isset ($_POST['updateElementName']) and !empty ($_POST['updateElementName'])) ? $cibleName = $this->Security->filter_form($_POST['updateElementName']) : $cibleName = '';

            //le prénom
            (isset ($_POST['updateElementFirstName']) and !empty ($_POST['updateElementFirstName'])) ? $cibleFirstName = $this->Security->filter_form($_POST['updateElementFirstName']) : $cibleFirstName = '';

            //le nom
            (isset ($_POST['updateElementLastName']) and !empty ($_POST['updateElementLastName'])) ? $cibleLastName = $this->Security->filter_form($_POST['updateElementLastName']) : $cibleLastName = '';

            //la date de naissance
            (isset ($_POST['updateElementBirthDate']) and !empty ($_POST['updateElementBirthDate'])) ? $cibleBirthDate = $this->Security->filter_form($_POST['updateElementBirthDate']) : $cibleBirthDate = null;

            //le pays
            (isset ($_POST['updateElementCountry']) and !empty ($_POST['updateElementCountry'])) ? $cibleCountry = $this->Security->filter_form($_POST['updateElementCountry']) : $cibleCountry = '';
           
            //cible active ?
            (isset ($_POST['updateElementActive']) and $_POST['updateElementActive'] === '1') ? $cibleActive = $this->Security->filter_form($_POST['updateElementActive']) : $cibleActive = '0';
            

            // on fait la modification en BDD et on récupère le résultat
            $res = $this->Cible->updateCible($cibleId, $cibleName, $cibleFirstName, $cibleLastName, $cibleBirthDate, $cibleCountry, $cibleActive);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-cible/action/success');
        exit;


    }

}
