<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Mission.php';


class MissionTest extends TestCase
{
   // Teste la méthode getAllMissions de la classe Mission
   public function testgetAllMissions()
   {

      $missionInstance = new Mission();

      // Appeler la méthode à tester
      $missionNames = $missionInstance->getAllMissions();

      // Vérifier si on a un tableau
      $this->assertIsArray($missionNames, 'La méthode getAllMissions devrait retourner un tableau.');
   }

   // Teste la méthode getSelectedMissions de la classe Mission
   public function testgetSelectedMissions()
   {

      $missionInstance = new Mission();

      // Appeler la méthode à tester
      $missionNames = $missionInstance->getSelectedMissions(1, 1, 1, 1, 1);

      // Vérifier si on a un tableau
      $this->assertIsArray($missionNames, 'La méthode getSelectedMissions devrait retourner un tableau.');
   }

      // Teste la méthode getSearchMissions de la classe Mission
      public function testgetSearchMissions()
      {
   
         $missionInstance = new Mission();
   
         // Appeler la méthode à tester
         $missionNames = $missionInstance->getSearchMissions('e');
   
         // Vérifier si on a un tableau
         $this->assertIsArray($missionNames, 'La méthode getSelectedMissions devrait retourner un tableau.');
      }

            // Teste la méthode getMission de la classe Mission
            public function testgetMission()
            {
         
               $missionInstance = new Mission();
         
               // Appeler la méthode à tester
               $missionNames = $missionInstance->getMission(1);
         
               // Vérifier si on a un tableau
               $this->assertIsArray($missionNames, 'La méthode getMission devrait retourner un tableau.');
            }
}