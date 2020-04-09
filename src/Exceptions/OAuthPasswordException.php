<?php

namespace App\Exceptions;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OAuthPasswordException extends AuthenticationException {

    public function getMessageKey()
    {
        return 'Your account was created via an external service 
            (Google, Github, Twig, etc.), you cannot change your password. Change your password from the service you used';
    }
}