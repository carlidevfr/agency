<?php

class HomeController {
    public function index(){
        $loader = new Twig\Loader\FilesystemLoader('./app/templates');
        $twig = new Twig\Environment($loader);
        
        $template = $twig->load('home.twig');

        echo  $template->render([
            'base_url' => BASE_URL,
        ]);    
}}