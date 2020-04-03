<?php

namespace App\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class LoginAfterRegistrationEvent extends Event {

    /**
     * @var UserInterface
     */
    private $user;
    /**
     * @var string
     */
    private $providerKey;

    public function __construct(UserInterface $user) {
        $this->user = $user;
        $this->providerKey = 'security.user.provider.concrete.from_database';
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getProviderKey(): string
    {
        return $this->providerKey;
    }

}