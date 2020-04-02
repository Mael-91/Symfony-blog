<?php

namespace App\Event;

use App\Entity\PasswordToken;
use Symfony\Contracts\EventDispatcher\Event;

class RequestChangePasswordEvent extends Event {

    /**
     * @var PasswordToken
     */
    private $token;

    public function __construct(PasswordToken $token) {
        $this->token = $token;
    }

    /**
     * @return PasswordToken
     */
    public function getToken(): PasswordToken
    {
        return $this->token;
    }
}