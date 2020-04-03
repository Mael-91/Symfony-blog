<?php

namespace App\Service;

use App\Entity\ConfirmationToken;

class TokenGeneratorService {

    const EXPIRE_IN = 2880; // 48H

    /**
     * Génère un token de taille aléatoire
     * @param int $lenght
     * @return string
     * @throws \Exception
     */
    public function generateToken(int $lenght = 20): string {
        $replace = ['+', '=', '/'];
        return rtrim(substr(str_replace($replace, '', bin2hex(random_bytes((int)ceil($lenght / 2)))), 2, $lenght));
    }

    /**
     * Permet de vérifier si un token est expiré
     * @param ConfirmationToken $token
     * @return bool
     * @throws \Exception
     */
    public function isExpired(ConfirmationToken $token): bool {
        $expiration = new \DateTime('-' . self::EXPIRE_IN . 'minutes');
        return $token->getCreatedAt() < $expiration;
    }
}