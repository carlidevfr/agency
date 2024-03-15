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

   public function testgetSearchSpecialityNames()
   // Teste la méthode getSearchSpecialityNames de la classe Speciality
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $specialityNames = $specialityInstance->getSearchSpecialityNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($specialityNames, 'La méthode getSearchSpecialityNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllSpecialityNames()
   // Teste la méthode getPaginationAllSpecialityNames de la classe Speciality
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $specialityNames = $specialityInstance->getPaginationAllSpecialityNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($specialityNames, 'La méthode getPaginationAllSpecialityNames devrait retourner un tableau.');
   }

   public function testgetBySpecialityId()
   // Teste la méthode getBySpecialityId de la classe Speciality
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $specialityNames = $specialityInstance->getBySpecialityId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($specialityNames, 'La méthode getBySpecialityId devrait retourner un tableau.');
   }

   public function testaddSpeciality()
   // Teste la méthode addSpeciality de la classe Speciality
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $result = $specialityInstance->addSpeciality('NouveauSpeciality');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La speciality suivant a bien été ajoutée : NouveauSpeciality', $result);
   }

   public function testEmptyaddSpeciality()
   // Teste la méthode addSpeciality de la classe Speciality avec une valeur vide
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $result = $specialityInstance->addSpeciality('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateSpeciality()
   // Teste la méthode updateSpeciality de la classe Speciality
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $result = $specialityInstance->updateSpeciality(1,'nouveau nom');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La speciality a bien été modifiée : nouveau nom', $result);   
   }

   public function testupdateSpecialityEmpty()
   // Teste la méthode updateSpeciality de la classe Speciality avec un changement vide
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $result = $specialityInstance->updateSpeciality(1,'');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testdeleteSpeciality()
   // Teste la méthode deleteSpeciality de la classe Speciality
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $result = $specialityInstance->deleteSpeciality(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('La speciality a bien été supprimée ', $result);
   }

   public function testdeleteSpecialityEmpty()
   // Teste la méthode deleteSpeciality de la classe Speciality avec une valeur vide
   {

      $specialityInstance = new Speciality();

      // Appeler la méthode à tester
      $result = $specialityInstance->deleteSpeciality('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}