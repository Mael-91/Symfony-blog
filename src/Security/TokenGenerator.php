<?php

namespace App\Security;

class TokenGenerator {

    /**
     * Permet de générer un chaine de caractère aléatoire
     *
     * @param int $lenght
     * @return string
     * @throws \Exception
     */
    public function generateToken(int $lenght = 20): string {
        $replace = ['+', '=', '/'];
        return rtrim(str_replace($replace, '', base64_encode(random_bytes($lenght))));
    }
}