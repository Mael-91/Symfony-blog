<?php

namespace App\Tests\Repository;

use App\DataFixtures\LoginAttemptFixture;
use App\Repository\LoginAttemptRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoginAttemptRepositoryTest extends KernelTestCase {

    use FixturesTrait;

    public function testCountAttempt() {
        self::bootKernel();
        $data = $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/LoginAttempt.yaml'
        ]);
        $repo = self::$container->get(LoginAttemptRepository::class)->countAttempt($data['user_attempt'], 15);
        $this->assertEquals(5, $repo);
    }
}