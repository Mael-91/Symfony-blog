<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Exceptions\TooManyBadCredentialsExeption;
use App\Repository\UserRepository;
use App\Security\LoginAuthenticator;
use App\Service\LoginAttemptService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LoginAuthenticatorTest extends TestCase {

    private $userRepositiry;
    private $authenticator;
    private $csrfToken;
    private $attemptService;

    public function setUp() {
        /** @var MockObject|EntityManagerInterface entityManager */
        $this->userRepositiry = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()->getMock();

        /** @var MockObject|UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()->getMock();

        /** @var MockObject|CsrfTokenManager csrfToken */
        $this->csrfToken = $this->getMockBuilder(CsrfTokenManagerInterface::class)->getMock();
        $this->csrfToken->expects($this->any())->method('isTokenValid')->willReturn(true);

        /** @var MockObject|UserPasswordEncoderInterface $encoder */
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $this->attemptService = $this->getMockBuilder(LoginAttemptService::class)
            ->disableOriginalConstructor()->getMock();

        /** @var MockObject|EventDispatcherInterface $dispatcher */
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->authenticator = new LoginAuthenticator(
            $this->userRepositiry,
            $urlGenerator,
            $this->csrfToken,
            $encoder,
            $this->attemptService,
            $dispatcher
        );
        parent::setUp();
    }

    public function testGetReturnedUser() {
        $provider = $this->getMockBuilder(UserProviderInterface::class)->getMock();

        $this->userRepositiry
            ->expects($this->once())
            ->method('findUserByUsernameOrEmail')
            ->with('Mael')
            ->willReturn(new User());

        $this->authenticator->getUser(['username' => 'Mael', 'csrf_token' => 'a'], $provider);
    }

    public function testThrowExceptionIfTooManyAttempts() {
        $this->attemptService
            ->expects($this->once())
            ->method('limitReachedFor')->willReturn(true);

        $user = new User();

        $this->expectException(TooManyBadCredentialsExeption::class);

        $this->authenticator->checkCredentials([], $user);
    }
}