<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CloudinaryDeleteEvent extends Event {

    public const NAME = 'cloudinary.delete.event';
    /**
     * @var string
     */
    private $folder;
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $file;

    public function __construct(string $folder, ?int $id, string $file) {

        $this->folder = $folder;
        $this->id = $id;
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

}