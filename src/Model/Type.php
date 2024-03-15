<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Type extends Model
{
    public function getAllTypeNames()
    {
        try {
            // retourne tous les type de missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idType AS id, typeName AS valeur
        FROM Types';
            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $type = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $type;
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

    public function getSearchTypeNames($TypeName, $page, $itemsPerPage)
    {
        try {

            // retourne les type recherchés
            // si vide tous les type

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idType AS id, typeName AS valeur
        FROM Types
        WHERE typeName LIKE :typeName
        ORDER BY idType
        LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($TypeName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':typeName', '%' . $TypeName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $type = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $type;
                    }
                } else {
                    return $this->getAllTypeNames();
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

    public function getPaginationAllTypeNames($page, $itemsPerPage)
    {
        try {
            // retourne tous les type triés par page

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idType AS id, typeName AS valeur
        FROM Types
        ORDER BY idType
        LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $type = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $type;
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

    public function getByTypeId($TypeId)
    {
        try {
            // retourne le type en fonction de son id

            $bdd = $this->connexionPDO();
            $req = '
            SELECT idType AS id, typeName AS valeur
            FROM Types
        WHERE idType  = :TypeId';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($TypeId)) {
                    $stmt->bindValue(':TypeId', $TypeId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $type = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $type;
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

    public function addType($typeName)
    {
        try {
            // Ajoute un type

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Types (typeName)
            VALUES (:typeName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($typeName)) {
                    $stmt->bindValue(':typeName', $typeName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le type suivant a bien été ajouté : ' . $typeName;
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

    public function deleteType($typeId)
    {
        try {
            // Supprime le type selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Types
            WHERE idType  = :typeId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($typeId)) {
                    $stmt->bindValue(':typeId', $typeId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le type a bien été supprimé ';
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

    public function updateType($TypeId, $newName)
    {
        try {
            // Modifie le type selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            UPDATE Types
            SET TypeName = :newTypeName
            WHERE idType  = :TypeId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($TypeId) and !empty($newName)) {
                    $stmt->bindValue(':TypeId', $TypeId, PDO::PARAM_INT);
                    $stmt->bindValue(':newTypeName', $newName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le type a bien été modifié : ' . $newName;
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