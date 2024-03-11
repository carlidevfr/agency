<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Status extends Model
{
    public function getAllStatusNames()
    {
        try {
            // retourne tous les status de missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idStatus AS id, statusName AS valeur
        FROM Status';
            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $status;
                }
            } else {
                return 'une erreur est survenue';
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

    public function getSearchStatusNames($StatusName, $page, $itemsPerPage)
    {
        try {

            // retourne les status recherchés
            // si vide tous les status

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idStatus AS id, statusName AS valeur
        FROM Status
        WHERE statusName LIKE :statusName
        ORDER BY idStatus
        LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($StatusName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':statusName', '%' . $StatusName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $status;
                    }
                } else {
                    return $this->getAllStatusNames();
                }
            } else {
                return 'une erreur est survenue';
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

    public function getPaginationAllStatusNames($page, $itemsPerPage)
    {
        try {
            // retourne tous les status triés par page

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idStatus AS id, statusName AS valeur
        FROM Status
        ORDER BY idStatus
        LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $status;
                    }
                }

            } else {
                return 'une erreur est survenue';
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

    public function getByStatusId($StatusId)
    {
        try {
            // retourne le status en fonction de son id

            $bdd = $this->connexionPDO();
            $req = '
            SELECT idStatus AS id, statusName AS valeur
            FROM Status
        WHERE idStatus  = :StatusId';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($StatusId)) {
                    $stmt->bindValue(':StatusId', $StatusId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $status = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $status;
                    }
                }
            } else {
                return 'une erreur est survenue';
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

    public function addStatus($statusName)
    {
        try {
            // Ajoute un status

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Status (statusName)
            VALUES (:statusName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($statusName)) {
                    $stmt->bindValue(':statusName', $statusName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le statut suivant a bien été ajouté : ' . $statusName;
                    }
                }
            } else {
                return 'une erreur est survenue';
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
            return 'Une erreur est survenue';
        }
    }

    public function deleteStatus($statusId)
    {
        try {
            // Supprime le status selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Status
            WHERE idStatus  = :statusId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($statusId)) {
                    $stmt->bindValue(':statusId', $statusId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le statut a bien été supprimé ';
                    }
                }
            } else {
                return 'une erreur est survenue';
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
            return 'Une erreur est survenue';
        }
    }

    public function updateStatus($StatusId, $newName)
    {
        try {
            // Modifie le status selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            UPDATE Status
            SET StatusName = :newStatusName
            WHERE idStatus  = :StatusId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($StatusId) and !empty($newName)) {
                    $stmt->bindValue(':StatusId', $StatusId, PDO::PARAM_INT);
                    $stmt->bindValue(':newStatusName', $newName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le statut a bien été modifié : ' . $newName;
                    }
                }
            } else {
                return 'une erreur est survenue';
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
            return 'Une erreur est survenue';
        }
    }
}