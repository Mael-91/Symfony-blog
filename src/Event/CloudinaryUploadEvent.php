<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\EventDispatcher\Event;

class CloudinaryUploadEvent extends Event {

    public const NAME = 'cloudinary.upload.event';
    /**
     * @var File
     */
    private $file;
    /**
     * @var string
     */
    private $folder;
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var int|null
     */
    private $width;
    /**
     * @var int|null
     */
    private $height;

    public function __construct(File $file, string $folder, ?int $id,  ?int $width, ?int $height) {
        $this->file = $file;
        $this->folder = $folder;
        $this->id = $id;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

}