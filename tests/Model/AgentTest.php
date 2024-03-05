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

}