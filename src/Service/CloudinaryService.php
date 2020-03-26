<?php

namespace App\Service;

use Cloudinary\Uploader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

class CloudinaryService {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Permet d'envoyer une image vers Cloudinary
     *
     * @param File $file
     * @param string $folder
     * @param int|null $id
     * @param int $width
     * @param int $height
     * @param bool $secure
     * @return array
     */
    public function uploadImageToCloudinary(File $file, string $folder, ?int $id, ?int $width, ?int $height, bool $secure = true): array {
        $this->config($secure);
        return Uploader::upload($file, [
            'folder' => $this->container->getParameter('app_name') . "/$folder/$id",
            'use_filename' => true,
            'unique_filename' => false,
            'resource_type' => 'image',
            'format' => 'jpg',
            'width' => $width,
            'height' => $height
        ]);
    }

    /**
     * Permet de supprimer une image depuis Cloudinary
     *
     * @param string $file
     * @param string $folder
     * @param int|null $id
     * @return array
     */
    public function deleteImageToCloudinary(string $folder, ?int $id, string $file): array {
        $this->config();
        $extension = ['.jpeg', '.png', '.jpg', '.gif'];
        $file = str_replace($extension, '', $file);
        if (!is_null($id)) {
            // TODO Suppression du fichier si l'id est défini
            return Uploader::destroy($this->container->getParameter('app_name') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file);
        }
        return Uploader::destroy($this->container->getParameter('app_name') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * Permet de faire la connexion avec le compte Cloudinary
     *
     * @param bool $secure
     * @return array|null
     */
    private function config(bool $secure = true): ?array {
        return \Cloudinary::config([
            'cloud_name' => 'mael',
            'api_key' => '797919775948942',
            'api_secret' => '5P3sBwJIH3O1pKT63ZhiP_N38x4',
            'secure' => $secure,
        ]);
    }
}