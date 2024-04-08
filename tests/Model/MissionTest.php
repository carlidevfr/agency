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

   public function testgetPaginationAllMissionNames()
   {

      $missionInstance = new Mission();

      // Appeler la méthode à tester
      $missionNames = $missionInstance->getPaginationAllMissionNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($missionNames, 'La méthode getPaginationAllMissionNames devrait retourner un tableau.');
   }

   public function testgetSearchMissionNames()
   {

      $missionInstance = new Mission();

      // Appeler la méthode à tester
      $missionNames = $missionInstance->getSearchMissionNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($missionNames, 'La méthode getSearchMissionNames devrait retourner un tableau.');
   }

   public function testgetAllMissionNames()
   {

      $missionInstance = new Mission();

      // Appeler la méthode à tester
      $missionNames = $missionInstance->getAllMissionNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($missionNames, 'La méthode getAllMissionNames devrait retourner un tableau.');
   }

   public function testAddMission()
   {
      $missionInstance = new Mission();

      // Données de test pour l'ajout d'une mission
      $title = "Nouvelle mission";
      $codeName = "ABC123";
      $description = "Description de la mission";
      $beginDate = "2024-04-15";
      $endDate = "2024-05-15";
      $missionCountry = 1; // ID d'un pays existant dans la base de données de test
      $missionType = 1; // ID d'un type de mission existant dans la base de données de test
      $missionStatus = 1; // ID d'un statut de mission existant dans la base de données de test
      $missionSpeciality = 1; // ID d'une spécialité existante dans la base de données de test
      $cibleIds = [1, 2, 3]; // ID des cibles existantes dans la base de données de test
      $contactIds = [6, 8]; // ID des contacts existants dans la base de données de test
      $agentIds = [7, 6]; // ID des agents existants dans la base de données de test
      $planqueIds = [1]; // ID des planques existantes dans la base de données de test

      // Exécution de la méthode addMission avec les données de test
      $result = $missionInstance->addMission(
         $title,
         $codeName,
         $description,
         $beginDate,
         $endDate,
         $missionCountry,
         $missionType,
         $missionStatus,
         $missionSpeciality,
         $cibleIds,
         $contactIds,
         $agentIds,
         $planqueIds
      );

      // Assertions pour vérifier le résultat de l'ajout de la mission
      $this->assertEquals("La mission a bien été créée.", $result);
   }

   public function testdeleteMission()
   // Teste la méthode deleteMission de la classe Mission
   {

      $MissionInstance = new Mission();

      // Appeler la méthode à tester
      $result = $MissionInstance->deleteMission(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Mission supprimée avec succès.', $result);
   }

   public function testupdateMission()
   {
      $missionInstance = new Mission();

      // Données de test pour la modif d'une mission
      $id = 1;
      $title = "Nouvelle mission";
      $codeName = "ABC123";
      $description = "Description de la mission";
      $beginDate = "2024-04-15";
      $endDate = "2024-05-15";
      $missionCountry = 2; // ID d'un pays existant dans la base de données de test
      $missionType = 2; // ID d'un type de mission existant dans la base de données de test
      $missionStatus = 2; // ID d'un statut de mission existant dans la base de données de test
      $missionSpeciality = 2; // ID d'une spécialité existante dans la base de données de test
      $cibleIds = [1, 4, 3]; // ID des cibles existantes dans la base de données de test
      $contactIds = [6]; // ID des contacts existants dans la base de données de test
      $agentIds = [7]; // ID des agents existants dans la base de données de test
      $planqueIds = [2]; // ID des planques existantes dans la base de données de test

      // Exécution de la méthode updateMission avec les données de test
      $result = $missionInstance->updateMission(
         $id,
         $title,
         $codeName,
         $description,
         $beginDate,
         $endDate,
         $missionCountry,
         $missionType,
         $missionStatus,
         $missionSpeciality,
         $cibleIds,
         $contactIds,
         $agentIds,
         $planqueIds
      );

      // Assertions pour vérifier le résultat de modification de la mission
      $this->assertEquals("La mission a bien été mise à jour.", $result);
   }

   public function testVerifyMissionConstraintsWithValidData()
   {
      $missionInstance = new Mission();

      // Données de test valides
      $missionCountry = 2;
      $missionSpeciality = 2;
      $cibleIds = [1, 2, 3];
      $contactIds = [8];
      $agentIds = [6];
      $planqueIds = 3;

      // Exécution de la méthode à tester
      $result = $missionInstance->verifyMissionConstraints(
         $missionCountry,
         $missionSpeciality,
         $cibleIds,
         $contactIds,
         $agentIds,
         $planqueIds
      );

      // Assertion
      $this->assertTrue($result);
   }

   public function testVerifyMissionConstraintsWithInvalidCibles()
   {
      $missionInstance = new Mission();
      // Données de test avec des cibles non conformes aux contraintes
      $missionCountry = 1;
      $missionSpeciality = 2;
      $cibleIds = [1, 2, 3, 4]; // Des cibles qui ne respectent pas les contraintes
      $contactIds = [4, 5];
      $agentIds = [6, 7, 8];
      $planqueIds = 9;

      // Exécution de la méthode à tester
      $result = $missionInstance->verifyMissionConstraints(
         $missionCountry,
         $missionSpeciality,
         $cibleIds,
         $contactIds,
         $agentIds,
         $planqueIds
      );

      // Assertion
      $this->assertFalse($result);
   }

   public function testVerifyMissionConstraintsWithInvalidContacts()
   {
      $missionInstance = new Mission();

      // Données de test avec des contacts non conformes aux contraintes
      $missionCountry = 1;
      $missionSpeciality = 2;
      $cibleIds = [1, 2, 3];
      $contactIds = [8]; // Un contact qui ne respecte pas les contraintes
      $agentIds = [6, 7, 8];
      $planqueIds = 9;

      // Exécution de la méthode à tester
      $result = $missionInstance->verifyMissionConstraints(
         $missionCountry,
         $missionSpeciality,
         $cibleIds,
         $contactIds,
         $agentIds,
         $planqueIds
      );

      // Assertion
      $this->assertFalse($result);
   }

   public function testVerifyMissionConstraintsWithInvalidPlanque()
   {
      $missionInstance = new Mission();

      // Données de test avec une planque non conforme aux contraintes
      $missionCountry = 1;
      $missionSpeciality = 2;
      $cibleIds = [1, 2, 3];
      $contactIds = [4, 5];
      $agentIds = [6, 7, 8];
      $planqueIds = 10; // Une planque qui ne respecte pas les contraintes

      // Exécution de la méthode à tester
      $result = $missionInstance->verifyMissionConstraints(
         $missionCountry,
         $missionSpeciality,
         $cibleIds,
         $contactIds,
         $agentIds,
         $planqueIds
      );

      // Assertion
      $this->assertFalse($result);
   }

}