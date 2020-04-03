<?php

namespace App\Event;

use App\Entity\ConfirmationToken;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class SecurityRegistrationEvent extends Event {

    /**
     * @var UserInterface
     */
    protected $user;
    /**
     * @var ConfirmationToken
     */
    private $token;

    public function __construct(User $user, ConfirmationToken $token) {
        $this->user = $user;
        $this->token = $token;
    }

    public function getUser() {
        return $this->user;
    }

    public function getToken(): ConfirmationToken {
        return $this->token;
    }
}