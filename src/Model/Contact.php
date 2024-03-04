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
}
