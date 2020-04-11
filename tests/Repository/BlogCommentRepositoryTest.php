<?php

namespace App\Tests;

use App\Repository\BlogCommentRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogCommentRepositoryTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testCountComment() {
        $this->assertEqualsMethodRepo('countComment', 10, 'BlogComment', BlogCommentRepository::class);
    }

    public function testFindLastComment() {
        $this->assertCountMethodRepo('findLastComment', 5, 'BlogComment', BlogCommentRepository::class);
    }
}
