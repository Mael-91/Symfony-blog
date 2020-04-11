<?php

namespace App\Tests;

use App\Entity\PasswordToken;
use App\Repository\PasswordTokenRepository;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PasswordServiceTest extends TestCase {

    /**
     * @var PasswordService
     */
    private $service;

    public function setUp() {
        $manager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $tokenRepository = $this->getMockBuilder(PasswordTokenRepository::class)->disableOriginalConstructor()->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $tokenGenerator = $this->getMockBuilder(TokenGeneratorService::class)->disableOriginalConstructor()->getMock();
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->service = new PasswordService(
            $manager,
            $tokenRepository,
            $userRepository,
            $encoder,
            $tokenGenerator,
            $dispatcher
        );
        parent::setUp();
    }

    public function testIsExpired() {
        $this->assertFalse($this->service->isExpired((new PasswordToken())->setCreatedAt(new \DateTime('-10 minutes'))));
        $this->assertTrue($this->service->isExpired((new PasswordToken())->setCreatedAt(new \DateTime('-40 minutes'))));
    }
}
