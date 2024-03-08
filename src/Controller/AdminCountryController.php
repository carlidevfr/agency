<?php
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
        //Récupère  la pagination
        (isset($_GET['page']) and !empty($_GET['page'])) ? $page = max(1, $this->Security->filter_form($_GET['page'])) : $page = 1;

        // Nombre d'éléments par page
        $itemsPerPage = 10;

        //Récupère le résultat de la recherche et la valeur de search pour permettre un get sur le search avec la pagination
        if (isset($_GET['search']) and !empty($_GET['search'])) {
            $countries = $this->Country->getSearchCountryNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);
        } else {
            $countries = $this->Country->getPaginationAllCountryNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        $pageMax = ceil(count($this->Country->getAllCountryNames()) / $itemsPerPage);

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminCountry.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'countries' => $countries,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search
        ]);
    }

    public function adminAddCountry()
    // Ajout de pays
    {
        // on récupère le pays ajouté
        (isset($_POST['addCountryName']) and !empty($_POST['addCountryName'])) ? $countryAction = $this->Security->filter_form($_POST['addCountryName']) : $countryAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Country->addCountry($countryAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        header('Location: ' . BASE_URL . '/admin/manage-country/action/success');

    }

    public function adminSuccessActionCountry()
    // Résultat succès ou echec après action sur pays
    {
        $res = null;

        if (isset($_SESSION['resultat']) and !empty($_SESSION['resultat'])) {
            $res = $this->Security->filter_form($_SESSION['resultat']);

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
        } else {

            //Si vide on retourne sur la page pays
            header('Location: ' . BASE_URL . '/admin/manage-country');
        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminCountry.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'addResult' => $res
        ]);

    }

    public function adminDeleteCountry()
    // Suppression de pays
    {
        // on récupère l'id pays à supprimer
        (isset($_POST['deleteCountryId']) and !empty($_POST['deleteCountryId'])) ? $countryAction = $this->Security->filter_form($_POST['deleteCountryId']) : $countryAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Country->deleteCountry($countryAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        header('Location: ' . BASE_URL . '/admin/manage-country/action/success');

    }

    public function adminUpdateCountryPage()
    // Modification de pays
    {
        //Récupère l'id du pays à modifier
        (isset($_GET['UpdateCountryId']) and !empty($_GET['UpdateCountryId'])) ? $countryAction = $this->Security->filter_form($_GET['UpdateCountryId']) : $countryAction = '';

        // Récupère le pays à modifier
        $country = $this->Country->getByCountryId($countryAction);
        $modifySection = true;
        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminCountry.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'country' => $country,
            'modifySection' => $modifySection
        ]);

    }

    public function adminUpdateCountry()
    // Modification de pays
    {
        // on récupère l'id pays à Modifier
        (isset($_POST['updateCountryId']) and !empty($_POST['updateCountryId'])) ? $countryAction = $this->Security->filter_form($_POST['updateCountryId']) : $countryAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset($_POST['updatedName']) and !empty($_POST['updatedName'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Country->updateCountry($countryAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        header('Location: ' . BASE_URL . '/admin/manage-country/action/success');

    }

}
