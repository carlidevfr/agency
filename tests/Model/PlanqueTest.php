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

}