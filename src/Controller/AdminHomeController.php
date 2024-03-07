<?php

require_once './src/Model/Common/Security.php';

class AdminHomeController
{

    public function adminHomePage(){
        $loader = new Twig\Loader\FilesystemLoader('./src/templates');
        $twig = new Twig\Environment($loader);
        $template = $twig->load('adminHome.twig');

        echo  $template->render([
            'base_url' => BASE_URL,
        ]);
    }

}
