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

}