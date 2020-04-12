<?php

namespace App\Tests;

use App\Repository\BlogRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogRepositoryTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testFindLastArticle() {
        $this->assertCountMethodRepo('findlastArticle', 5, 'Blog', BlogRepository::class);
    }

    public function testCountPost() {
        $this->assertEqualsMethodRepo('countPost', 12, 'Blog', BlogRepository::class);
    }
}
