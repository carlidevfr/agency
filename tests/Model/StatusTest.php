<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Status.php';


class StatusTest extends TestCase
{
   // Teste la méthode getAllStatusNames de la classe Status
   public function testgetgetAllStatusNames()
   {

    $statusInstance = new Status();

    // Appeler la méthode à tester
    $statusNames = $statusInstance->getAllStatusNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($statusNames, 'La méthode getAllStatusNames devrait retourner un tableau.');
   }
}