<?php
require_once './src/Model/Mission.php';
require_once './src/Model/Speciality.php';
require_once './src/Model/Common/Security.php';

class AdminSpecialityController
{
    private $Missions;
    private $Speciality;
    private $Security;

    public function __construct()
    {
        $this->Missions = new Mission();
        $this->Speciality = new Speciality();
        $this->Security = new Security();

    }

    public function adminSpecialityPage()
    // Accueil admin de la section speciality
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
            $speciality = $this->Speciality->getSearchSpecialityNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();
            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $speciality = $this->Speciality->getPaginationAllSpecialityNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus. Dans un if pour éviter toute erreur
        if (!empty ($this->Speciality->getAllSpecialityNames())) {
            $pageMax = ceil(count($this->Speciality->getAllSpecialityNames()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'speciality',
            'elements' => $speciality,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => 'admin/manage-speciality/delete',
            'addUrl' => 'admin/manage-speciality/add',
            'updateUrl' => 'admin/manage-speciality/update',
            'previousUrl' => 'admin/manage-speciality',
            'token' => $token
        ]);
    }
    public function adminSuccessActionSpeciality()
    // Résultat succès ou echec après action sur speciality
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
            (isset ($idElement) and !empty ($idElement) ? $data = $this->Speciality->getRelatedSpeciality($idElement) : $data = '');

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page speciality
            header('Location: ' . BASE_URL . 'admin/manage-speciality');
        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'speciality',
            'addResult' => $res,
            'data' => $data,
            'deleteUrl' => 'admin/manage-speciality/delete',
            'addUrl' => 'admin/manage-speciality/add',
            'updateUrl' => 'admin/manage-speciality/update',
            'previousUrl' => 'admin/manage-speciality'
        ]);

    }

    public function adminAddSpeciality()
    // Ajout de speciality
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère le speciality ajouté et le token
        (isset ($_POST['addElementName']) and !empty ($_POST['addElementName']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $specialityAction = $this->Security->filter_form($_POST['addElementName']) : $specialityAction = '';

        // on fait l'ajout en BDD et on récupère le résultat
        $res = $this->Speciality->addSpeciality($specialityAction);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-speciality/action/success');
        exit;

    }

    public function adminDeleteSpeciality()
    // Suppression de speciality
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id speciality à supprimer
        (isset ($_POST['deleteElementId']) and !empty ($_POST['deleteElementId']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $specialityAction = $this->Security->filter_form($_POST['deleteElementId']) : $specialityAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Speciality->deleteSpeciality($specialityAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $specialityAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-speciality/action/success');
        exit;


    }

    public function adminUpdateSpecialityPage()
    // Page permettant la saisie pour la modification de speciality
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du speciality à modifier et vérifie si la requête est authentifiée
        (isset ($_GET['UpdateElementId']) and !empty ($_GET['UpdateElementId']) and isset ($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $specialityAction = $this->Security->filter_form($_GET['UpdateElementId']) : $specialityAction = '';

        // Récupère le speciality à modifier
        $speciality = $this->Speciality->getByspecialityId($specialityAction);
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
            'pageName' => 'speciality',
            'elements' => $speciality,
            'modifySection' => $modifySection,
            'deleteUrl' => 'admin/manage-speciality/delete',
            'addUrl' => 'admin/manage-speciality/add',
            'updateUrl' => 'admin/manage-speciality/update',
            'previousUrl' => 'admin/manage-speciality',
            'token' => $token
        ]);

    }

    public function adminUpdateSpeciality()
    // Modification de speciality
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id speciality à Modifier
        (isset ($_POST['updateElementId']) and !empty ($_POST['updateElementId']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $specialityAction = $this->Security->filter_form($_POST['updateElementId']) : $specialityAction = '';

        // on récupère le nouveau nom et on vérifie qu'il n'est pas vide
        (isset ($_POST['updatedName']) and !empty ($_POST['updatedName']) and isset ($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $newName = $this->Security->filter_form($_POST['updatedName']) : $newName = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Speciality->updateSpeciality($specialityAction, $newName);

        // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . 'admin/manage-speciality/action/success');
        exit;
    }

}
