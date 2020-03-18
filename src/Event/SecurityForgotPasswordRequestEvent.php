<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class SecurityForgotPasswordRequestEvent extends Event {

    public const NAME = 'security.forgot.password.request';
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user->getUsername();
    }

    public function getEmail() {
        return $this->user->getEmail();
    }

    public function getToken() {
        return $this->user->getPasswordToken();
    }

}