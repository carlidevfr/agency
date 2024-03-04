<?php

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Model.php';
require_once './src/Model/Common/Security.php';

// Comme Model est une classe abstraite, on l'importe dans une classe
class TestModel extends Model
{}

class ModelTest extends TestCase
{
    private $TestModel;

    protected function setUp(): void
    // Avant chaque test de la classe
    {
        $this->TestModel =  new TestModel;
    }

    protected function tearDown(): void
    // Après chaque test de la classe
    {
        $this->TestModel =  null;
    }


    public function testConnexionPDO()
    {
        $bdd = $this->TestModel->connexionPDO();
        // on vérifie qu'on obtient bien un objet pdo et qu'il n'est pas null
        $this->assertInstanceOf(PDO::class, $bdd, 'La connexion PDO devrait retourner un objet PDO.');
        $this->assertNotNull($bdd, 'La connexion PDO ne devrait pas être nulle.');
    }

    /**
     * @runInSeparateProcess
     */

    public function testSendJSON()
    {
        // test de la méthode sendJSON
        ob_start();
        $this->TestModel->sendJSON(["key" => "value"]);
        $output = ob_get_clean();

        // Vérifier si la sortie est un JSON valide
        $decodedOutput = json_decode($output, true);
        $this->assertNotNull($decodedOutput);
        $this->assertEquals(["key" => "value"], $decodedOutput);
    }
}
