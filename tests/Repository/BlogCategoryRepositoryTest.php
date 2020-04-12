<?php

namespace App\Tests;

use App\Repository\BlogCategoryRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogCategoryRepositoryTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testcountCategory() {
        $this->assertEqualsMethodRepo('countCategory', 7, 'BlogCategory', BlogCategoryRepository::class);
    }
}
