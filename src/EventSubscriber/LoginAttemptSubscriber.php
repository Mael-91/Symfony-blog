<?php

namespace App\EventSubscriber;

use App\Event\BadPasswordEvent;
use App\Event\BadPasswordLoginEvent;
use App\Service\LoginAttemptService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginAttemptSubscriber implements EventSubscriberInterface {

    /**
     * @var LoginAttemptService
     */
    private $service;

    public function __construct(LoginAttemptService $service) {

        $this->service = $service;
    }

    public static function getSubscribedEvents()
    {
        return [
            BadPasswordLoginEvent::class => 'onSecurityAuthenticationFailure',
        ];
    }

    public function onSecurityAuthenticationFailure(BadPasswordLoginEvent $event): void {
        $this->service->addAttempt($event->getUser());
    }
}
