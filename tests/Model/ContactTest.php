<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Contact.php';


class ContactTest extends TestCase
{
   // Teste la méthode getCiblesByIdMission de la classe Contact
   public function testgetContactsByIdMission()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->getContactsByIdMission(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($contactNames, 'La méthode getContactsByIdMission devrait retourner un tableau.');
   }

}