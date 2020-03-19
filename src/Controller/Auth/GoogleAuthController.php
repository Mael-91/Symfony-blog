<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Event\SecurityRegistrationEvent;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GoogleAuthController extends AbstractController {

    private $googleId;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var TokenGeneratorService
     */
    private $tokenGenerator;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct($googleId, UrlGeneratorInterface $urlGenerator, SessionInterface $session, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, TokenGeneratorService $tokenGenerator, EventDispatcherInterface $dispatcher) {

        $this->googleId = $googleId;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->dispatcher = $dispatcher;
    }

    public function connect(): Response {
        $url = $this->urlGenerator->generate('google_connect', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $state = $this->session->set('oauth-google-state', base64_encode(random_bytes(30)));
        return new RedirectResponse("https://accounts.google.com/o/oauth2/v2/auth?scope=openid%20profile%20email&access_type=online&response_type=code&state=$state&redirect_uri=$url&client_id=$this->googleId");
    }

    public function generateAccount(string $username, string $email, string $firstName, string $familyName) {
        $user = new User();
        $user->setUsername($username);
        $user->setFirstName($firstName);
        $user->setLastName($familyName);
        $user->setEmail($email);
        $password = $this->passwordEncoder->encodePassword($user, random_bytes(20));
        $user->setPassword($password);
        $user->setEnabled(false);
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setRequestedTokenAt(new \DateTime());
        $user->setCreatedAt(new \DateTime());
        $this->manager->persist($user);
        $this->manager->flush();
        $registerMail = new SecurityRegistrationEvent($user);
        $this->dispatcher->dispatch($registerMail, SecurityRegistrationEvent::NAME);

        return $user;
    }
}