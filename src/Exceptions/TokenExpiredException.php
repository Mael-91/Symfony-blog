<?php

namespace App\Exceptions;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenExpiredException extends AuthenticationException {

    public function getMessageKey()
    {
        return 'The token has expired';
    }

}