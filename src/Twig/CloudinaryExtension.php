<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CloudinaryExtension extends AbstractExtension {

    public function getFilters()
    {
        return [
            new TwigFilter('cloud_image', [$this, 'getCloudImage'])
        ];
    }

    public function getCloudImage(string $filename, string $projectName, string $folder): string {
        $filename = substr($filename, -26);
        return "https://res.cloudinary.com/mael/image/upload/$projectName/$folder/$filename";
    }

}