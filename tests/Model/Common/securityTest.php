<?php
session_start();

use PHPUnit\Framework\TestCase;

require_once './src/Model/Common/Security.php';


class SecurityTest extends TestCase
{
    public function testFilterForm()
    {
        // Cas où la donnée est une chaîne vide
        $data = '';
        $filtered_data = Security::filter_form($data);
        $this->assertSame('', $filtered_data);

        // Cas où la donnée est une chaîne avec des espaces
        $data = '   test   ';
        $filtered_data = Security::filter_form($data);
        $this->assertSame('test', $filtered_data);

        // Cas où la donnée contient des caractères spéciaux
        $data = '<script>alert("XSS");</script>';
        $filtered_data = Security::filter_form($data);
        $this->assertSame('&lt;script&gt;alert(&quot;XSS&quot;);&lt;/script&gt;', $filtered_data);
    }

    public function testVerifyToken()
    {
        // Cas où les tokens sont identiques
        $token = 'valid_token';
        $form = 'valid_token';
        $this->assertTrue(Security::verifyToken($token, $form));

        // Cas où les tokens sont différents
        $token = 'valid_token';
        $form = 'invalid_token';
        $this->assertFalse(Security::verifyToken($token, $form));

        // Cas où l'un des tokens est vide
        $token = '';
        $form = 'valid_token';
        $this->assertFalse(Security::verifyToken($token, $form));

        // Cas où l'un des tokens est NULL
        $token = null;
        $form = 'valid_token';
        $this->assertFalse(Security::verifyToken($token, $form));
    }

}
