<?php

namespace App\Tests;

use App\Entity\LoginAttempt;
use App\Entity\User;
use App\Repository\LoginAttemptRepository;
use App\Service\LoginAttemptService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoginAttemptServiceTest extends KernelTestCase {

    use FixturesTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repo;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $manager;
    /**
     * @var LoginAttemptService
     */
    private $service;

    public function setUp() {
        $this->manager = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
        $this->repo = $this->getMockBuilder(LoginAttemptRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new LoginAttemptService($this->manager, $this->repo);
        parent::setUp();
    }

    public function testAddAttempt() {
        $user = new User();

        $this->manager->expects($this->once())->method('persist')->with(
            $this->callback(function (LoginAttempt $attempt) use ($user) {
                return $attempt->getUser() === $user;
            })
        );

        $this->manager->expects($this->once())->method('flush');

        $this->service->addAttempt($user);
    }

    public function testLimitReachedFor() {
        $this->assertLmitReachedFor(5, 'assertTrue');
    }

    public function testLimitNotReachedFor() {
        $this->assertLmitReachedFor(1, 'assertFalse');
    }

    private function assertLmitReachedFor(int $willReturn, string $assert) {
        self::bootKernel();
        $data = $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/LoginAttempt.yaml'
        ]);
        $this->repo->method('countAttempt')->with($data['user_attempt'])->willReturn($willReturn);
        $this->$assert($this->service->limitReachedFor($data['user_attempt']));
    }
}
