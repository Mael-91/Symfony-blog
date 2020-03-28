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

class TwitchAuthController extends AbstractController {

    private $twitchId;
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
     * @var \App\Service\TokenGeneratorService
     */
    private $tokenGenerator;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        $twitchId,
        UrlGeneratorInterface $urlGenerator,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager,
        TokenGeneratorService $tokenGenerator,
        EventDispatcherInterface $dispatcher) {

        $this->twitchId = $twitchId;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->dispatcher = $dispatcher;
    }

    public function connect() {
        $url = $this->urlGenerator->generate('twitch_connect', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $state = $this->session->set('oauth-twitch-state', random_bytes(20));
        $claims = '{"id_token":{"email":null,"email_verified":null},"userinfo":{"picture":null}}';
        return new RedirectResponse("https://id.twitch.tv/oauth2/authorize?client_id=$this->twitchId&redirect_uri=$url&response_type=code&scope=user:read:email&claims=$claims");
    }

    public function generateAccount(string $username, string $email) {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $password = $this->passwordEncoder->encodePassword($user, random_bytes(20));
        $user->setPassword($password);
        $user->setEnabled(false);
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setRequestedTokenAt(new \DateTime());
        $user->setCreatedAt(new \DateTime());
        $user->setOauth(true);
        $this->manager->persist($user);
        $this->manager->flush();
        $registerMail = new SecurityRegistrationEvent($user);
        $this->dispatcher->dispatch($registerMail, SecurityRegistrationEvent::NAME);

        return $user;
    }
}