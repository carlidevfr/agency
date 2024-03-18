<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';
require_once './src/Model/Status.php';


class StatusTest extends TestCase
{
   public function testgetAllStatusNames()
   // Teste la méthode getAllStatusNames de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $statusNames = $statusInstance->getAllStatusNames();

      // Vérifier si on a un tableau
      $this->assertIsArray($statusNames, 'La méthode getAllStatusNames devrait retourner un tableau.');
   }

   public function testgetSearchStatusNames()
   // Teste la méthode getSearchStatusNames de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $statusNames = $statusInstance->getSearchStatusNames('e', 1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($statusNames, 'La méthode getSearchStatusNames devrait retourner un tableau.');
   }

   public function testgetPaginationAllStatusNames()
   // Teste la méthode getPaginationAllStatusNames de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $statusNames = $statusInstance->getPaginationAllStatusNames(1, 10);

      // Vérifier si on a un tableau
      $this->assertIsArray($statusNames, 'La méthode getPaginationAllStatusNames devrait retourner un tableau.');
   }

   public function testgetByStatusId()
   // Teste la méthode getByStatusId de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $statusNames = $statusInstance->getByStatusId(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($statusNames, 'La méthode getByStatusId devrait retourner un tableau.');
   }

   public function testgetRelatedStatus()
   // Teste la méthode getRelatedStatus de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $statusNames = $statusInstance->getRelatedStatus(1);

      // Vérifier si on a un tableau
      $this->assertIsArray($statusNames, 'La méthode getRelatedStatus devrait retourner un tableau.');
   }

   public function testaddStatus()
   // Teste la méthode addStatus de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $result = $statusInstance->addStatus('NouveauStatut');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le statut suivant a bien été ajouté : NouveauStatut', $result);
   }

   public function testEmptyaddStatus()
   // Teste la méthode addStatus de la classe Status avec une valeur vide
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $result = $statusInstance->addStatus('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testupdateStatus()
   // Teste la méthode updateStatus de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $result = $statusInstance->updateStatus(1,'nouveau nom');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le statut a bien été modifié : nouveau nom', $result);   
   }

   public function testupdateStatusEmpty()
   // Teste la méthode updateStatus de la classe Status avec un changement vide
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $result = $statusInstance->updateStatus(1,'');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }

   public function testdeleteStatus()
   // Teste la méthode deleteStatus de la classe Status
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $result = $statusInstance->deleteStatus(1000);

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals('Le statut a bien été supprimé ', $result);
   }

   public function testdeleteStatusEmpty()
   // Teste la méthode deleteStatus de la classe Status avec une valeur vide
   {

      $statusInstance = new Status();

      // Appeler la méthode à tester
      $result = $statusInstance->deleteStatus('');

      // Vérifie que le résultat est conforme aux attentes
      $this->assertEquals(null, $result);
   }
}