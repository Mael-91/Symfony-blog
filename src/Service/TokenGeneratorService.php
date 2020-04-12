<?php

namespace App\Service;

use App\Entity\ConfirmationToken;

class TokenGeneratorService {

    const EXPIRE_IN = 2880; // 48H

    /**
     * Génère un token de taille aléatoire
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function generateToken(int $length = 20): string {
        return rtrim(substr(bin2hex(random_bytes((int)ceil($length / 2))), 0, $length));
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