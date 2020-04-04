<?php

namespace App\Exceptions;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class UserNotConnectedException extends AccountStatusException {

    public function getMessageKey()
    {
        return 'You must be logged in';
    }
}