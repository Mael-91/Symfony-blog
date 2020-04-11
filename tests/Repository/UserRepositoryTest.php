<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use App\Tests\AssertTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testCountUser() {
        $this->assertEqualsMethodRepo('countUser', 17, 'User', UserRepository::class);
    }

    public function testCountWithOAuth() {
        $this->assertEqualsMethodRepo('countUserWithOAuth', 5, 'User', UserRepository::class);
    }

    public function testFindLastUser() {
        $this->assertCountMethodRepo('findLastUser', 5, 'User', UserRepository::class);
    }
}