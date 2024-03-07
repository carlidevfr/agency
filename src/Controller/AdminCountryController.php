<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Country.php';
require_once './src/Model/Common/Security.php';

class AdminCountryController
{
    private $Missions;
    private $Country;
    private $Security;

    public function __construct(){
        $this->Missions = new Mission();
        $this->Country = new Country();
        $this->Security = new Security();

    }

    public function adminCountryPage(){
        $countries = $this->Country->getAllCountryNames();
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminCountry.twig');

        echo  $template->render([
            'base_url' => BASE_URL,
            'countries'=> $countries
        ]);
    }

    public function adminSearchCountryPage(){
        // on récupère la recherche
        (isset($_POST['search']) AND !empty($_POST['search'])) ? $search = $this->Security->filter_form($_POST['search']) : $search = '';
        (isset($_POST['selectCountry']) AND !empty($_POST['selectCountry'])) ? $selectCountry = $this->Security->filter_form($_POST['search']) : $selectCountry = '';
        (isset($_POST['Supprimer']) AND !empty($_POST['Supprimer'])) ? $countryAction = $this->Security->filter_form($_POST['Supprimer']) : $countryAction = '';
        (isset($_POST['Modifier']) AND !empty($_POST['Modifier'])) ? $countryAction = $this->Security->filter_form($_POST['Modifier']) : $countryAction = '';
        (isset($_POST['Ajouter']) AND !empty($_POST['Ajouter'])) ? $countryAction = $this->Security->filter_form($_POST['Ajouter']) : $countryAction = '';

        $countries = $this->Country->getSearchCountryNames($search);
        
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminCountry.twig');
        echo $_POST['selectCountry'];
        echo $_POST['Supprimer'];
        echo $_POST['Modifier'];
        echo $_POST['Ajouter'];

        echo  $template->render([
            'base_url' => BASE_URL,
            'countries'=> $countries
        ]);
    }
    public function adminAddCountry(){
        // on récupère la recherche
        (isset($_POST['search']) AND !empty($_POST['search'])) ? $search = $this->Security->filter_form($_POST['search']) : $search = '';
        (isset($_POST['selectCountry']) AND !empty($_POST['selectCountry'])) ? $selectCountry = $this->Security->filter_form($_POST['search']) : $selectCountry = '';
        (isset($_POST['Supprimer']) AND !empty($_POST['Supprimer'])) ? $countryAction = $this->Security->filter_form($_POST['Supprimer']) : $countryAction = '';
        (isset($_POST['Modifier']) AND !empty($_POST['Modifier'])) ? $countryAction = $this->Security->filter_form($_POST['Modifier']) : $countryAction = '';
        (isset($_POST['Ajouter']) AND !empty($_POST['Ajouter'])) ? $countryAction = $this->Security->filter_form($_POST['Ajouter']) : $countryAction = '';

        $countries = $this->Country->getSearchCountryNames($search);
        
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminCountry.twig');
        echo $_POST['selectCountry'];
        echo $_POST['Supprimer'];
        echo $_POST['Modifier'];
        echo $_POST['Ajouter'];

        echo  $template->render([
            'base_url' => BASE_URL,
            'countries'=> $countries
        ]);
    }
}
