<?php

class Security
{
    // Cette fonction filtre les données d'un formulaire en enlevant les espaces inutiles en début et fin de chaîne, en supprimant les antislashes ajoutés pour échapper les caractères spéciaux et en convertissant les caractères spéciaux en entités HTML. Elle renvoie les données filtrées.
    public static function filter_form($data)
    {
        $data = trim((string)$data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }


}
