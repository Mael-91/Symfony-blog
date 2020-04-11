<?php

namespace App\Tests;

use App\Entity\LoginAttempt;
use App\Entity\User;
use App\Repository\LoginAttemptRepository;
use App\Service\LoginAttemptService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LoginAttemptServiceTest extends TestCase {

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repo;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $manager;

    public function setUp() {
        $this->manager = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
        $this->repo = $this->getMockBuilder(LoginAttemptRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testAddAttempt() {
        $service = new LoginAttemptService($this->manager, $this->repo);
        $user = new User();

        $this->manager->expects($this->once())->method('persist')->with(
            $this->callback(function (LoginAttempt $attempt) use ($user) {
                return $attempt->getUser() === $user;
            })
        );

        $this->manager->expects($this->once())->method('flush');

        $service->addAttempt($user);
    }
}
