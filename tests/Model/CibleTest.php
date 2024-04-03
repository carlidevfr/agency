<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Cible.php';


class CibleTest extends TestCase
{
   // Teste la méthode getCiblesByIdMission de la classe Cible
   public function testgetCiblesByIdMission()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->getCiblesByIdMission(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($cibleNames, 'La méthode getCiblesByIdMission devrait retourner un tableau.');
   }

   public function testgetSearchCibleNames()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->getSearchCibleNames('e',1,10);

      // Vérifier si on a un tableau
      $this->assertIsArray($cibleNames, 'La méthode getSearchCibleNames devrait retourner un tableau.');
   }

   public function testgetAllCibleNames()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->getAllCibleNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($cibleNames, 'La méthode getAllCibleNames devrait retourner un tableau.');
   }

   public function testgetByCibleId()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->getByCibleId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($cibleNames, 'La méthode getByCibleId devrait retourner un tableau.');
   }

   public function testgetRelatedCible()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->getRelatedCible(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($cibleNames, 'La méthode getRelatedCible devrait retourner un tableau.');
   }

   public function testgetPaginationAllCibleNames()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->getPaginationAllCibleNames(1,10);

      // Vérifier si on a un tableau
      $this->assertIsArray($cibleNames, 'La méthode getPaginationAllCibleNames devrait retourner un tableau.');
   }

   public function testaddCible()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->addCible('toto', 'toto', 'toto', '1990/01/01', 1, 1);

      // Vérifier si on a un tableau
      $this->assertEquals('La cible suivante a bien été ajoutée : toto', $cibleNames);
   }

   public function testdeleteCible()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->deleteCible(1000);

      // Vérifier si on a un tableau
      $this->assertEquals('La cible a bien été supprimée ', $cibleNames);
   }

   public function testupdateCible()
   {

      $cibleInstance = new Cible();

      // Appeler la méthode à tester
      $cibleNames = $cibleInstance->updateCible(1, 'toto', 'toto', 'toto', '1990/01/01', 1, 1);

      // Vérifier si on a un tableau
      $this->assertEquals('La cible a bien été modifiée : toto', $cibleNames);
   }
}