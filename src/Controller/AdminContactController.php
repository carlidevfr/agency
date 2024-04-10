<?php
use PHPUnit\Framework\Constraint\IsEmpty;

require_once './src/Model/Mission.php';
require_once './src/Model/Contact.php';
require_once './src/Model/Country.php';
require_once './src/Model/Common/Security.php';

class AdminContactController
{
    private $Missions;
    private $Contact;
    private $Country;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Contact = new Contact();
        $this->Security = new Security();
        $this->Country = new Country();
    }

    public function adminContactPage()
    // Accueil admin de la section contact
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
            $contacts = $this->Contact->getSearchContactNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $contacts = $this->Contact->getPaginationAllContactNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty ($this->Contact->getAllContactNames())) {
            $pageMax = ceil(count($this->Contact->getAllContactNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        // On récupère la liste des personnes qui ne sont pas des contacts pour un éventuel add
        $cibles = $this->Contact->getNotContactNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'contacts',
            'elements' => $contacts,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'cibles' => $cibles,
            'deleteUrl' => 'admin/manage-contact/delete',
            'addUrl' => 'admin/manage-contact/add',
            'updateUrl' => 'admin/manage-contact/update',
            'previousUrl' => 'admin/manage-contact',
            'token' => $token
        ]);
    }
    public function adminSuccessActionContact()
    // Résultat succès ou echec après action sur contact
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
            (isset ($idElement) and !empty ($idElement) ? $data = $this->Contact->getRelatedContact($idElement) : $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page contact
            header('Location: ' . BASE_URL . 'admin/manage-contact');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'contacts',
            'addResult' => $res,
            'data' => $data,
            'deleteUrl' => 'admin/manage-contact/delete',
            'addUrl' => 'admin/manage-contact/add',
            'updateUrl' => 'admin/manage-contact/update',
            'previousUrl' => 'admin/manage-contact'
        ]);

    }

    public function adminAddContact()
    // Ajout de contact
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // l id
            (isset ($_POST['addElementId']) and !empty ($_POST['addElementId'])) ? $contactName = $this->Security->filter_form($_POST['addElementId']) : $contactName = '';

            // on fait l'ajout en BDD et on récupère le résultat
            $res = $this->Contact->addContact($contactName);

            // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
            $_SESSION['resultat'] = $res;
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-contact/action/success');
        exit;
    }

    public function adminDeleteContact()
    // Suppression de contact
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id contact à supprimer
        (isset ($_POST['deleteElementId']) and !empty ($_POST['deleteElementId']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $contactAction = $this->Security->filter_form($_POST['deleteElementId']) : $contactAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Contact->deleteContact($contactAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $contactAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-contact/action/success');
        exit;


    }


}
