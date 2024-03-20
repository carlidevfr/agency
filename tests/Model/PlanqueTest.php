<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Planque.php';


class PlanqueTest extends TestCase
{
   // Teste la méthode getPlanquesByIdMission de la classe Planque
   public function testgetPlanquesByIdMission()
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $planqueNames = $planqueInstance->getPlanquesByIdMission(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($planqueNames, 'La méthode getPlanquesByIdMission devrait retourner un tableau.');
   }

   public function testgetAllPlanqueNames()
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $planqueNames = $planqueInstance->getAllPlanqueNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($planqueNames, 'La méthode getAllPlanqueNames devrait retourner un tableau.');
   }

   public function testgetSearchPlanqueNames()
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $planqueNames = $planqueInstance->getSearchPlanqueNames('e',1,10);

      // Vérifier si on a un tableau
      $this->assertIsArray($planqueNames, 'La méthode getSearchPlanqueNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllPlanqueNames()
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $planqueNames = $planqueInstance->getPaginationAllPlanqueNames(1,10);

      // Vérifier si on a un tableau
      $this->assertIsArray($planqueNames, 'La méthode getPaginationAllPlanqueNames devrait retourner un tableau.');
   }

   public function getByPlanqueId()
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $planqueNames = $planqueInstance->getByPlanqueId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($planqueNames, 'La méthode getByPlanqueId devrait retourner un tableau.');
   }

   public function testaddPlanque()
   // Teste la méthode addPlanque de la classe planque
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $result = $planqueInstance->addPlanque('NouveauPlanque','33',1,1,'type');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La planque suivant a bien été ajoutée : NouveauPlanque', $result);
   }

   public function testEmptyaddPlanque()
   // Teste la méthode addPlanque de la classe Planque avec une valeur vide
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $result = $planqueInstance->addPlanque('','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdatePlanque()
   // Teste la méthode updatePlanque de la classe Planque
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $result = $planqueInstance->updatePlanque(1,'nouveau nom','test',1,1,'type');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La planque a bien été modifiée : nouveau nom', $result);   
   }

   public function testupdatePlanqueEmpty()
   // Teste la méthode updatePlanque de la classe Planque avec un changement vide
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $result = $planqueInstance->updatePlanque('','','','','','');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testdeletePlanque()
   // Teste la méthode deletePlanque de la classe Planque
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $result = $planqueInstance->deletePlanque(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La planque a bien été supprimée ', $result);
   }

   public function testdeletePlanqueEmpty()
   // Teste la méthode deletePlanque de la classe Planque avec une valeur vide
   {

      $planqueInstance = new Planque();

      // Appeler la méthode à tester
      $result = $planqueInstance->deletePlanque('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

}