<?php

namespace App\Tests\Twig;

use App\Twig\CloudinaryExtension;
use PHPUnit\Framework\TestCase;

class CloudinaryExtensionTest extends TestCase
{
    /**
     * @var CloudinaryExtension
     */
    private $cloudinaryExtension;

    public function setUp() {
        $this->cloudinaryExtension = new CloudinaryExtension();
    }

    public function testRenderLink() {
        $filename = 'my-image.jpg';
        $link = "https://res.cloudinary.com/mael/image/upload/test/blog/$filename";
        $this->assertSame($link, $this->cloudinaryExtension->getCloudImage($filename, 'test', 'blog'));
    }
}
