<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Type.php';


class TypeTest extends TestCase
{
   // Teste la méthode getAllTypeNames de la classe Type
   public function testgetAllTypeNames()
   {

    $typeInstance = new Type();

    // Appeler la méthode à tester
    $typeNames = $typeInstance->getAllTypeNames();

    // Vérifier si on a un tableau
    $this->assertIsArray($typeNames, 'La méthode getAllTypeNames devrait retourner un tableau.');
   }

   public function testgetSearchTypeNames()
   // Teste la méthode getSearchTypeNames de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $typeNames = $typeInstance->getSearchTypeNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($typeNames, 'La méthode getSearchTypeNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllTypeNames()
   // Teste la méthode getPaginationAllTypeNames de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $typeNames = $typeInstance->getPaginationAllTypeNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($typeNames, 'La méthode getPaginationAllTypeNames devrait retourner un tableau.');
   }

   public function testgetByTypeId()
   // Teste la méthode getByTypeId de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $typeNames = $typeInstance->getByTypeId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($typeNames, 'La méthode getByTypeId devrait retourner un tableau.');
   }

   public function testgetRelatedType()
   // Teste la méthode getRelatedType de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $typeNames = $typeInstance->getRelatedType(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($typeNames, 'La méthode getRelatedType devrait retourner un tableau.');
   }


   public function testaddType()
   // Teste la méthode addType de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $result = $typeInstance->addType('NouveauType');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le type suivant a bien été ajouté : NouveauType', $result);
   }

   public function testEmptyaddType()
   // Teste la méthode addType de la classe Type avec une valeur vide
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $result = $typeInstance->addType('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateType()
   // Teste la méthode updateType de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $result = $typeInstance->updateType(1,'nouveau nom');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le type a bien été modifié : nouveau nom', $result);   
   }

   public function testupdateTypeEmpty()
   // Teste la méthode updateType de la classe Type avec un changement vide
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $result = $typeInstance->updateType(1,'');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testdeleteType()
   // Teste la méthode deleteType de la classe Type
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $result = $typeInstance->deleteType(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le type a bien été supprimé ', $result);
   }

   public function testdeleteTypeEmpty()
   // Teste la méthode deleteType de la classe Type avec une valeur vide
   {

      $typeInstance = new Type();

      // Appeler la méthode à tester
      $result = $typeInstance->deleteType('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}