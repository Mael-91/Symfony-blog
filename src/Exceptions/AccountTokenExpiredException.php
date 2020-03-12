<?php

namespace App\Exceptions;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountTokenExpiredException extends AccountStatusException {

    public function getMessageKey()
    {
        return 'The account validation token has expired';
    }
}