<?php
require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

class Contact extends Model
{
    public function getContactsByIdMission($idMission)
    {
        try {
            // retourne tous les status de missions

            $bdd = $this->connexionPDO();
            $req = '
        SELECT
            Contacts.idContact,
            Cibles.firstname,
            Cibles.lastname,
            Cibles.birthdate,
            Cibles.codeName,
            Country.countryName AS countryCible
        FROM
            Contacts
        JOIN
            Cibles ON Contacts.idContact = Cibles.idCible
        JOIN
            ContactsInMission ON Contacts.idContact = ContactsInMission.idContact
        JOIN
            Missions ON ContactsInMission.idMission = Missions.idMission
        JOIN
            Country ON Cibles.countryCible = Country.idCountry
        WHERE
            Missions.idMission = :idMission;';

            $stmt = $bdd->prepare($req);

            if (!empty($idMission)) {
                $stmt->bindValue(':idMission', $idMission, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $contacts;
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

    public function getAllContactNames()
    // retourne tous les contact
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
        SELECT
        Contacts.idContact AS id,
        Cibles.firstname,
        Cibles.lastname,
        DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
        Cibles.codeName AS valeur,
        Country.idCountry AS contactCountryId,
        Country.countryName AS countryName
    FROM
        Contacts
    JOIN
        Cibles ON Contacts.idContact = Cibles.idCible
    JOIN
        Country ON Cibles.countryCible = Country.idCountry";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $contact;
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

    public function getNotContactNames()
    // retourne toutes les personnes qui ne sont pas des contacts
    {
        try {
            $bdd = $this->connexionPDO();
            $req = "
            SELECT Cibles.idCible AS id,
            Cibles.codeName AS valeur
            FROM Cibles
            WHERE idCible NOT IN (SELECT idContact FROM Contacts)";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if ($stmt->execute()) {
                    $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $contact;
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

    public function getSearchContactNames($ContactName, $page, $itemsPerPage)
    // retourne les contacts recherchés
    // si vide tous les contacts
    {

        try {
            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
        SELECT
            Contacts.idContact AS id,
            Cibles.firstname,
            Cibles.lastname,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Cibles.codeName AS valeur,
            Country.countryName AS countryName
        FROM
            Contacts
        JOIN
            Cibles ON Contacts.idContact = Cibles.idCible
        JOIN
            Country ON Cibles.countryCible = Country.idCountry
        WHERE Cibles.codeName LIKE :contactName
        ORDER BY Contacts.idContact
        LIMIT :offset, :itemsPerPage";
            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($ContactName) and !empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':contactName', '%' . $ContactName . '%', PDO::PARAM_STR);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $contact;
                    }
                } else {
                    return $this->getAllContactNames();
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

    public function getPaginationAllContactNames($page, $itemsPerPage)
    // retourne tous les contact triés par page
    {
        try {
            

            // Calculez l'offset pour la requête : Page 1,2 etc
            $offset = ($page - 1) * $itemsPerPage;

            $bdd = $this->connexionPDO();
            $req = "
            SELECT
            Contacts.idContact AS id,
            Cibles.firstname,
            Cibles.lastname,
            DATE_FORMAT(Cibles.birthdate, '%d/%m/%Y') AS formattedBirthdate,
            Cibles.codeName AS valeur,
            Country.countryName AS countryName
        FROM
            Contacts
        JOIN
            Cibles ON Contacts.idContact = Cibles.idCible
        JOIN
            Country ON Cibles.countryCible = Country.idCountry
        ORDER BY idContact
        LIMIT :offset, :itemsPerPage";

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($page) and !empty($itemsPerPage)) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        return $contact;
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

    public function getRelatedContact($contactId)
    // Récupère tous les éléments liés à un contact
    {
        try {
           // Initialisation de la liste des éléments liés
            $relatedElements = array();

            // Liste des tables avec des clés étrangères vers Cible
            $tables = array(
                'Contacts' => 'idContact',
            );

            // Boucle sur les tables pour récupérer les éléments liés
            foreach ($tables as $tableName => $foreignKey) {

                $bdd = $this->connexionPDO();
                $req = "SELECT Missions.codeName
                FROM Contacts
                INNER JOIN ContactsInMission ON Contacts.idContact = ContactsInMission.idContact
                INNER JOIN Missions ON ContactsInMission.idMission = Missions.idMission
                WHERE Contacts.idContact =  :ContactId";

                // on teste si la connexion pdo a réussi
                if (is_object($bdd)) {
                    $stmt = $bdd->prepare($req);

                    if (!empty ($contactId) and !empty ($contactId)) {
                        $stmt->bindValue(':ContactId', $contactId, PDO::PARAM_INT);
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

    public function addContact($contactId)
    // Ajoute un contact
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            INSERT INTO Contacts (idContact)
            VALUES (:contactName)';

            if (is_object($bdd)) {
                // on teste si la connexion pdo a réussi
                $stmt = $bdd->prepare($req);

                if (!empty($contactId)) {
                    $stmt->bindValue(':contactName', $contactId, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        return 'Le contact a bien été ajouté ';
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

    public function deleteContact($contactId)
    // Supprime le contact selon l'id
    {
        try {
            $bdd = $this->connexionPDO();
            $req = '
            DELETE FROM Contacts
            WHERE idContact  = :contactId';

            // on teste si la connexion pdo a réussi
            if (is_object($bdd)) {
                $stmt = $bdd->prepare($req);

                if (!empty($contactId)) {
                    $stmt->bindValue(':contactId', $contactId, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        return 'Le contact a bien été supprimé ';
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
