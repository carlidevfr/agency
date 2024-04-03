<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Agent.php';


class AgentTest extends TestCase
{
   // Teste la méthode getAllAgentNames de la classe Agent
   public function testgetAllAgentNames()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getAllAgentNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getAllAgentNames devrait retourner un tableau.');
   }

   public function testgetAgentsByIdMission()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getAgentsByIdMission(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getAgentsByIdMission devrait retourner un tableau.');
   }

   public function testgetNotAgentNames()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getNotAgentNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getNotAgentNames devrait retourner un tableau.');
   }
   public function testgetSearchAgentNames()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getSearchAgentNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getSearchAgentNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllAgentNames()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getPaginationAllAgentNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getPaginationAllAgentNames devrait retourner un tableau.');
   }

   public function testgetByAgentId()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getByAgentId(6);

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getByAgentId devrait retourner un tableau.');
   }

   public function testgetRelatedAgent()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->getRelatedAgent(6);

      // Vérifier si on a un tableau
      $this->assertIsArray($agentNames, 'La méthode getRelatedAgent devrait retourner un tableau.');
   }

   public function testaddAgent()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->addAgent(3,'eee','');

      // Vérifier si on a un tableau
      $this->assertEquals(null, $agentNames);
   }

   public function testupdateAgent()
   // Teste la méthode updateAgent de la classe Planque
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->updateAgent(3,'nouveau nom',[1,2,3]);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Cet agent a été modifié avec succès.', $agentNames);   
   }

   public function testdeleteAgent()
   {

      $agentInstance = new Agent();

      // Appeler la méthode à tester
      $agentNames = $agentInstance->deleteAgent(3);

      // Vérifier si on a un tableau
      $this->assertEquals('Cet agent a été supprimé avec succès.', $agentNames);
   }



}