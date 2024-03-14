<?php

class Security
{
    // Cette fonction filtre les données d'un formulaire en enlevant les espaces inutiles en début et fin de chaîne, en supprimant les antislashes ajoutés pour échapper les caractères spéciaux et en convertissant les caractères spéciaux en entités HTML. Elle renvoie les données filtrées.
    public static function filter_form($data)
    {
        $data = trim((string) $data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    public static function verifyAccess()
    // on vérifie si l'utilisateur a le droit d'être là, sinon on détruit la session et on le redirige vers l'accueil
    {
        //On vérifie si l'adresse ip est la même que lors de la connexion
        //Si le navigateur est le même
        //Si le rôle est admin
        // si un token existe
        if (
            isset($_SESSION['ipAdress']) and $_SESSION['ipAdress'] === $_SERVER['REMOTE_ADDR'] and
            isset($_SESSION['userAgent']) and $_SESSION['userAgent'] === $_SERVER['HTTP_USER_AGENT'] and
            isset($_SESSION['role']) and $_SESSION['role'] === 'admin' and
            isset($_SESSION['csrf_token'])
        ) {
            // On regénère le token
            $_SESSION['csrf_token'] = md5(bin2hex(openssl_random_pseudo_bytes(6)));

            // On vérifie si on regénère l'id de session
            if (isset($_SESSION['last_id']) and time() - $_SESSION['last_id'] > 10) {
                session_regenerate_id(true);
                $_SESSION['last_id'] = time();
            }

            // on autorise la connexion
            return true;
        }else{
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL.'/login');
            exit;
        }
    }
}
