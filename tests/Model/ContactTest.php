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

   public function testgetAllContactNames()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->getAllContactNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($contactNames, 'La méthode getAllContactNames devrait retourner un tableau.');
   }

   public function testgetNotContactNames()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->getNotContactNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($contactNames, 'La méthode getNotContactNames devrait retourner un tableau.');
   }

   public function testgetSearchContactNames()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->getSearchContactNames('e',1,10);

      // Vérifier si on a un tableau
      $this->assertIsArray($contactNames, 'La méthode getSearchContactNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllContactNames()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->getPaginationAllContactNames(1,10);

      // Vérifier si on a un tableau
      $this->assertIsArray($contactNames, 'La méthode getPaginationAllContactNames devrait retourner un tableau.');
   }

   public function testgetRelatedContact()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->getRelatedContact(5);

      // Vérifier si on a un tableau
      $this->assertIsArray($contactNames, 'La méthode getRelatedContact devrait retourner un tableau.');
   }

   public function testaddContact()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->addContact(4);

      // Vérifier si on a un tableau
      $this->assertEquals('Le contact a bien été ajouté ', $contactNames);
   }

   public function testdeleteContact()
   {

      $contactInstance = new Contact();

      // Appeler la méthode à tester
      $contactNames = $contactInstance->deleteContact(4);

      // Vérifier si on a un tableau
      $this->assertEquals('Le contact a bien été supprimé ', $contactNames);
   }
}