<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Type.php';


class TypeTest extends TestCase
{
   // Teste la méthode getAllTypesNames de la classe Type
   public function testgetAllTypesNames()
   {

    $typeInstance = new Type();

    // Appeler la méthode à tester
    $typeNames = $typeInstance->getAllTypesNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($typeNames, 'La méthode getAllTypesNames devrait retourner un tableau.');
   }
}