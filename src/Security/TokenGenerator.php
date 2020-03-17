<?php

namespace App\Security;

use App\Exceptions\AccountTokenExpiredException;

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

    /**
     * Permet de vérifier la temps de validité d'un jeton
     *
     * @param \DateTime|null $requestedAt
     * @return bool
     * @throws \Exception
     */
    public function isRequestInTime(\DateTime $requestedAt = null): bool {
        if (is_null($requestedAt)) {
            throw new AccountTokenExpiredException();
        }

        $now = new \DateTime('now');
        $interval = $now->getTimestamp() - $requestedAt->getTimestamp();
        $validTime = 60 * 15;
        $isValid = $interval > $validTime ? false : $isValid = true;
        return $isValid;
    }
}