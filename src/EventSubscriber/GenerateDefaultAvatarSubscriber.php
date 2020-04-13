<?php

namespace App\EventSubscriber;

use App\Entity\AvatarGenerator;
use App\Event\GenerateAvatarEvent;
use App\Service\AvatarGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GenerateDefaultAvatarSubscriber implements EventSubscriberInterface {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var AvatarGeneratorService
     */
    private $service;

    public function __construct(EntityManagerInterface $manager, AvatarGeneratorService $service) {
        $this->manager = $manager;
        $this->service = $service;
    }

    public static function getSubscribedEvents() {
        return [
            GenerateAvatarEvent::class => 'onGenerateAvatarEvent'
        ];
    }

    public function onGenerateAvatarEvent(GenerateAvatarEvent $event): void {
        $color = $this->service->generateColor();
        $default = new AvatarGenerator();
        $default->setUser($event->getUser())->setColor($color);
        $this->manager->persist($default);
        $this->manager->flush();
    }
}