<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Speciality.php';


class SpecialityTest extends TestCase
{
   // Teste la méthode getAllCountryNames de la classe Speciality
   public function testgetAllSpecialityNames()
   {

    $specialityInstance = new Speciality();

    // Appeler la méthode à tester
    $specialityNames = $specialityInstance->getAllSpecialityNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($specialityNames, 'La méthode getAllSpecialityNames devrait retourner un tableau.');
   }
}