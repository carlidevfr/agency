<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Admin extends Model
{
    public function adminIsValid($email, $password)
    //vérifie si l'utilisateur existe
    {
        try {
            // récupère le mot de passe hashé

            $bdd = $this->connexionPDO();
            $req = '
            SELECT password
            FROM Admins
            WHERE email  = :email
            LIMIT 1';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($email)) {
                    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $bddPassword = $stmt->fetchColumn();
                        $stmt->closeCursor();
                        if (isset($bddPassword) and !empty($bddPassword) and password_verify($password, $bddPassword)) {
                            //si on a un résultat et si le hash correspond au mot de passe renseigné
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    return 'une erreur est survenue';
                }
            }
        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }

    public function getAdminId($email, $password)
    //vérifie si l'utilisateur existe
    {
        try {
            // Vérifie si l'id et le mdp correspondent à un utilisateur
            if ($this->adminIsValid($email, $password) === true) {
                //Si oui, on récupère l'UUID
                $bdd = $this->connexionPDO();
                $req = '
                SELECT idAdmin
                FROM Admins
                WHERE email  = :email
                LIMIT 1';

                if (is_object($bdd)) {
                    // on teste si la connexion pdo a réussi
                    $stmt = $bdd->prepare($req);

                    if (!empty($email)) {
                        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

                        if ($stmt->execute()) {
                            $idAdmin = $stmt->fetchColumn();
                            $stmt->closeCursor();
                            return $idAdmin;
                        }
                    } else {
                        return 'une erreur est survenue';
                    }
                }


            } else {
                return 'Erreur de connexion';
            }

        } catch (Exception $e) {
            $log = sprintf(
                "%s %s %s %s %s",
                date('Y-m-d- H:i:s'),
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine()
            );
            error_log($log . "\n\r", 3, './src/error.log');
        }
    }



}