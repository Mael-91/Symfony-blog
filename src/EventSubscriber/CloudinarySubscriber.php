<?php

namespace App\EventSubscriber;

use App\Event\CloudinaryDeleteEvent;
use App\Event\CloudinaryUploadEvent;
use App\Service\CloudinaryService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CloudinarySubscriber implements EventSubscriberInterface
{

    /**
     * @var CloudinaryService
     */
    private $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService) {

        $this->cloudinaryService = $cloudinaryService;
    }

    public static function getSubscribedEvents()
    {
        return [
            'cloudinary.upload.event' => 'onCloudinaryUploadEvent',
            'cloudinary.delete.event' => 'onCloudinaryDeleteEvent',
        ];
    }

    public function onCloudinaryUploadEvent(CloudinaryUploadEvent $event)
    {
        return $this->cloudinaryService->uploadImageToCloudinary($event->getFile(), $event->getFolder(),
            $event->getId(), $event->getWidth(), $event->getHeight());
    }

    public function onCloudinaryDeleteEvent(CloudinaryDeleteEvent $event) {
        return $this->cloudinaryService->deleteImageToCloudinary($event->getFolder(), $event->getId(), $event->getFile());
    }
}
