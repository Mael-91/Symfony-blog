<?php

namespace App\Tests;

use App\Repository\BlogLikeRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogLikeRepositoryTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testCountLike() {
        $this->assertEqualsMethodRepo('countLike', 5, 'BlogLike', BlogLikeRepository::class);
    }
}
