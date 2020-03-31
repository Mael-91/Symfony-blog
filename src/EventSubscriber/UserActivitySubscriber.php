<?php

namespace App\EventSubscriber;

use App\Entity\UserActivity;
use App\Event\UserActivityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserActivitySubscriber implements EventSubscriberInterface {

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserActivityEvent::class => 'onUserActivityEvent',
        ];
    }

    public function onUserActivityEvent(UserActivityEvent $event): void {
        $activity = new UserActivity();
        $activity->setUser($event->getUser())
            ->setBlog($event->getBlog())
            ->setBlogComment($event->getBlogComment())
            ->setBlogLike($event->getBlogLike());
        $this->manager->persist($activity);
        $this->manager->flush();
    }
}
