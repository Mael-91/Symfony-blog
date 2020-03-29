<?php

namespace App\Exceptions;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TooManyBadCredentialsExeption extends AuthenticationException {

    public function getMessageKey()
    {
        return 'Too many attempt';
    }
}