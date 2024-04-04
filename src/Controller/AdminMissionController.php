<?php
use PHPUnit\Framework\Constraint\IsEmpty;

require_once './src/Model/Mission.php';
require_once './src/Model/Common/Security.php';

class AdminMissionController
{
    private $Mission;
    private $Security;



    public function __construct()
    {
        $this->Mission = new Mission();
        $this->Mission = new Mission();
        $this->Security = new Security();
    }

    public function adminMissionPage()
    // Accueil admin de la section mission
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
            $missions = $this->Mission->getSearchMissionNames($this->Security->filter_form($_GET['search']), $page, $itemsPerPage);
            $search = $this->Security->filter_form($_GET['search']);

            // on regénère le token
            $this->Security->regenerateToken();

            // On récupère le token
            $token = $this->Security->getToken();
        } else {
            $missions = $this->Mission->getPaginationAllMissionNames($page, $itemsPerPage);
            $search = '';
        }

        // Récupère le nombre de pages, on arrondi au dessus
        if (!empty($this->Mission->getAllMissions())) {
            $pageMax = ceil(count($this->Mission->getAllMissions()) / $itemsPerPage);
        } else {
            $pageMax = 1;
        }

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'missions',
            'elements' => $missions,
            'pageMax' => $pageMax,
            'activePage' => $page,
            'search' => $search,
            'deleteUrl' => '/admin/manage-mission/delete',
            'addUrl' => '/admin/manage-mission/add',
            'updateUrl' => '/admin/manage-mission/update',
            'previousUrl' => '/admin/manage-mission',
            'token' => $token
        ]);
    }
    public function adminSuccessActionMission()
    // Résultat succès ou echec après action sur mission
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

            // Efface les résultats de la session pour éviter de les conserver plus longtemps que nécessaire
            unset($_SESSION['resultat']);
            unset($_SESSION['idElement']);

        } else {

            //Si vide on retourne sur la page mission
            header('Location: ' . BASE_URL . '/admin/manage-mission');
            exit;

        }

        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'missions',
            'addResult' => $res,
            'deleteUrl' => '/admin/manage-mission/delete',
            'addUrl' => '/admin/manage-mission/add',
            'updateUrl' => '/admin/manage-mission/update',
            'previousUrl' => '/admin/manage-mission'
        ]);

    }

    public function adminAddMission()
    // Ajout de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // l id
            (isset($_POST['addElementId']) and !empty($_POST['addElementId'])) ? $missionId = $this->Security->filter_form($_POST['addElementId']) : $missionId = '';

            // le nom de code
            (isset($_POST['addElementCodeName']) and !empty($_POST['addElementCodeName'])) ? $missionName = $this->Security->filter_form($_POST['addElementCodeName']) : $missionName = '';

            // la liste des spécialités
            (isset($_POST['addElementSpe']) and !empty($_POST['addElementSpe'])) ? $missionSpeList = $this->Security->filter_form_array($_POST['addElementSpe']) : $missionSpeList = '';

            if (isset($missionId) && isset($missionName) && isset($missionSpeList) && !empty($missionId) && !empty($missionName) && !empty($missionSpeList)) {
                // Les variables $missionId, $missionName et $missionSpeList existent et ne sont pas vides

                // on fait l'ajout en BDD et on récupère le résultat
                $res = $this->Mission->addMission($missionId, $missionName, $missionSpeList);
                if (empty($res)) {
                    $res = 'Cet mission a bien été ajouté';
                }
                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;
            } else {
                // on indique qu'il y a une erreur
                $res = 'une erreur est survenue';

                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;
            }
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-mission/action/success');
        exit;
    }

    public function adminDeleteMission()
    // Suppression de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère l'id mission à supprimer
        (isset($_POST['deleteElementId']) and !empty($_POST['deleteElementId']) and isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) ? $missionAction = $this->Security->filter_form($_POST['deleteElementId']) : $missionAction = '';

        // on fait la suppression en BDD et on récupère le résultat
        $res = $this->Mission->deleteMission($missionAction);

        // Stockage des résultats et l'id de l'élément dans la session puis redirection pour éviter renvoi au rafraichissement
        $_SESSION['resultat'] = $res;
        $_SESSION['idElement'] = $missionAction;

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-mission/action/success');
        exit;


    }

    public function adminUpdateMissionPage()
    // Page permettant la saisie pour la modification de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        //Récupère l'id du mission à modifier
        (isset($_GET['UpdateElementId']) and !empty($_GET['UpdateElementId']) and isset($_GET['tok']) and $this->Security->verifyToken($token, $_GET['tok'])) ? $missionAction = $this->Security->filter_form($_GET['UpdateElementId']) : $missionAction = '';

        // Récupère l'e 'mission à modifier
        $mission = $this->Mission->getByMissionId($missionAction);
        $modifySection = true;

        // on regénère le token
        $this->Security->regenerateToken();

        // On récupère le token pour le nouveau form
        $token = $this->Security->getToken();

        // On récupère la liste des spécialités
        $spe = $this->speciality->getAllSpecialityNames();

        //twig
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminManageElement.twig');

        echo $template->render([
            'base_url' => BASE_URL,
            'pageName' => 'missions',
            'elements' => $mission,
            'spe' => $spe,
            'modifySection' => $modifySection,
            'deleteUrl' => '/admin/manage-mission/delete',
            'addUrl' => '/admin/manage-mission/add',
            'updateUrl' => '/admin/manage-mission/update',
            'previousUrl' => '/admin/manage-mission',
            'token' => $token
        ]);

    }

    public function adminUpdateMission()
    // Modification de mission
    {
        //On vérifie si on a le droit d'être là (admin)
        $this->Security->verifyAccess();

        // On récupère le token
        $token = $this->Security->getToken();

        // on récupère la planque ajoutée et le token
        if (isset($_POST['tok']) and $this->Security->verifyToken($token, $_POST['tok'])) {
            // l'id
            (isset($_POST['updateElementId']) and !empty($_POST['updateElementId'])) ? $missionId = $this->Security->filter_form($_POST['updateElementId']) : $missionId = '';

            // le nom de code
            (isset($_POST['updateElementName']) and !empty($_POST['updateElementName'])) ? $missionName = $this->Security->filter_form($_POST['updateElementName']) : $missionName = '';

            // la liste des spécialités
            (isset($_POST['updateElementSpe']) and !empty($_POST['updateElementSpe'])) ? $missionSpeList = $this->Security->filter_form_array($_POST['updateElementSpe']) : $missionSpeList = '';

            if (isset($missionId) && isset($missionName) && isset($missionSpeList) && !empty($missionId)) {
                // Les variables $missionId, $missionName et $missionSpeList existent

                // on fait la modification en BDD et on récupère le résultat
                $res = $this->Mission->updateMission($missionId, $missionName, $missionSpeList);

                // Stockage des résultats dans la session puis redirection pour éviter renvoi au rafraichissement
                $_SESSION['resultat'] = $res;
            }
        }

        // on regénère le token
        $this->Security->regenerateToken();

        header('Location: ' . BASE_URL . '/admin/manage-mission/action/success');
        exit;
    }

}
