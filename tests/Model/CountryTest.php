<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Country.php';


class CountryTest extends TestCase
{
   // Teste la méthode getAllCountryNames de la classe Country
   public function testgetAllCountryNames()
   {

    $countryInstance = new Country();

    // Appeler la méthode à tester
    $countryNames = $countryInstance->getAllCountryNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($countryNames, 'La méthode getAllCountryNames devrait retourner un tableau.');
   }
}