<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Country extends Model
{
    public function getSearchCountryNames($countryName, $page, $itemsPerPage)
    {
        try {

            // retourne les pays recherchés
            // si vide tous les pays

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idCountry AS id, countryName AS valeur
        FROM Country
        WHERE countryName LIKE :countryName
        ORDER BY idCountry
        LIMIT :offset, :itemsPerPage';
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($countryName) and !empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':countryName', '%' . $countryName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $country = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $country;
                    }
                } else {
                    return $this->getAllCountryNames();
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

    public function getAllCountryNames()
    {
        try {
            // retourne tous les pays

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idCountry AS id, countryName AS valeur
        FROM Country';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $country = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $country;
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

    public function getByCountryId($countryId)
    {
        try {
            // retourne tous les pays

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idCountry AS id, countryName AS valeur
        FROM Country
        WHERE idCountry  = :countryId';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($countryId)) {
                    $stmt->bindValue(':countryId', $countryId, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $country = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $country;
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

    public function getRelatedCountries($countryId)
    // Récupère tous les éléments liés à un pays
    {
        try {

            // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers Country
            $tables = array(
                'Cibles' => 'countryCible',
                'Planques' => 'planqueCountry',
                'Missions' => 'missionCountry'
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "SELECT * FROM $tableName WHERE $foreignKey = :countryId";

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty ($countryId) and !empty ($countryId)) {
                        $stmt->bindValue(':countryId', $countryId, PDO::PARAM_INT);
                        if ($stmt->execute()) {
                            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $stmt->closeCursor();

                            // Ajout des résultats à la liste
                            $relatedElements[$tableName] = $results;
                        } else {
                            return 'une erreur est survenue';
                        }
                    }
                } else {
                    return 'une erreur est survenue';
                }
            }

            // Retourne la liste des éléments liés
            return $relatedElements;

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

    public function getPaginationAllCountryNames($page, $itemsPerPage)
    {
        try {
            // retourne tous les pays triés par page

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = '
        SELECT idCountry AS id, countryName AS valeur
        FROM Country
        ORDER BY idCountry
        LIMIT :offset, :itemsPerPage';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($page) and !empty ($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $country = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $country;
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

    public function addCountry($countryName)
    {
        try {
            // Ajoute un pays

            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Country (countryName)
            VALUES (:countryName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty ($countryName)) {
                    $stmt->bindValue(':countryName', $countryName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le pays suivant a bien été ajouté : ' . $countryName;
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

    public function deleteCountry($countryId)
    {
        try {
            // Supprime le pays selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Country
            WHERE idCountry  = :countryId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($countryId)) {
                    $stmt->bindValue(':countryId', $countryId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le pays a bien été supprimé ';
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

    public function updateCountry($countryId, $newName)
    {
        try {
            // Modifie le pays selon l'id

            $bdd = $this->connexionPDO();
            $req = '
            UPDATE Country
            SET countryName = :newCountryName
            WHERE idCountry  = :countryId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty ($countryId) and !empty ($newName)) {
                    $stmt->bindValue(':countryId', $countryId, PDO::PARAM_INT);
                    $stmt->bindValue(':newCountryName', $newName, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le pays a bien été modifié : ' . $newName;
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
