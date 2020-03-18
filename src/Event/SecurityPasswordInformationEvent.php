<?php


namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class SecurityPasswordInformationEvent extends Event {

    public const NAME = 'security.reset.password.information';
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
}