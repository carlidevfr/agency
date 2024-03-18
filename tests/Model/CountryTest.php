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

   public function testgetSearchCountryNames()
   // Teste la méthode getSearchCountryNames de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $CountryNames = $CountryInstance->getSearchCountryNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($CountryNames, 'La méthode getSearchCountryNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllCountryNames()
   // Teste la méthode getPaginationAllCountryNames de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $CountryNames = $CountryInstance->getPaginationAllCountryNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($CountryNames, 'La méthode getPaginationAllCountryNames devrait retourner un tableau.');
   }

   public function testgetByCountryId()
   // Teste la méthode getByCountryId de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $CountryNames = $CountryInstance->getByCountryId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($CountryNames, 'La méthode getByCountryId devrait retourner un tableau.');
   }

   public function testgetRelatedCountries()
   // Teste la méthode getRelatedCountries de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $CountryNames = $CountryInstance->getRelatedCountries(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($CountryNames, 'La méthode getRelatedCountries devrait retourner un tableau.');
   }

   public function testaddCountry()
   // Teste la méthode addCountry de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $result = $CountryInstance->addCountry('Nouveaupays');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le pays suivant a bien été ajouté : Nouveaupays', $result);
   }

   public function testEmptyaddCountry()
   // Teste la méthode addCountry de la classe Country avec une valeur vide
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $result = $CountryInstance->addCountry('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateCountry()
   // Teste la méthode updateCountry de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $result = $CountryInstance->updateCountry(1,'nouveau nom');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le pays a bien été modifié : nouveau nom', $result);   
   }

   public function testupdateCountryEmpty()
   // Teste la méthode updateCountry de la classe Country avec un changement vide
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $result = $CountryInstance->updateCountry(1,'');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testdeleteCountry()
   // Teste la méthode deleteCountry de la classe Country
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $result = $CountryInstance->deleteCountry(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le pays a bien été supprimé ', $result);
   }

   public function testdeleteCountryEmpty()
   // Teste la méthode deleteCountry de la classe Country avec une valeur vide
   {

      $CountryInstance = new Country();

      // Appeler la méthode à tester
      $result = $CountryInstance->deleteCountry('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}