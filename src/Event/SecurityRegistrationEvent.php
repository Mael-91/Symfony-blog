<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class SecurityRegistrationEvent extends Event {

    public const NAME = 'security.registration';

    /**
     * @var UserInterface
     */
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user->getUsername();
    }

    public function getEmail() {
        return $this->user->getEmail();
    }

    public function getId() {
        return $this->user->getId();
    }

    public function getConfirmationToken() {
        return $this->user->getConfirmationToken();
    }

}