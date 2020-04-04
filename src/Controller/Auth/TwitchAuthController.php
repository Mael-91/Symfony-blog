<?php

namespace App\Controller\Auth;

use App\Entity\ConfirmationToken;
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
     * @var TokenGeneratorService
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
        $state = $this->session->set('oauth-twitch-state', random_bytes(20)); // TODO Ajouter le state dans l'url
        $claims = '{"id_token":{"email":null,"email_verified":null},"userinfo":{"picture":null}}';
        return new RedirectResponse("https://id.twitch.tv/oauth2/authorize?client_id=$this->twitchId&redirect_uri=$url&response_type=code&scope=user:read:email&claims=$claims");
    }

    public function generateAccount(string $username, string $email): User {
        $user = new User();
        $token = new ConfirmationToken();
        $user->setUsername($username)
            ->setEmail($email)
            ->setPassword($this->passwordEncoder->encodePassword($user, random_bytes(20)))
            ->setRoles([User::DEFAULT_ROLE])
            ->setOauth(true);
        $this->manager->persist($user);
        $token->setUser($user)
            ->setToken($this->tokenGenerator->generateToken(10));
        $this->manager->persist($token);
        $this->manager->flush();
        $this->addFlash('success', 'Well done, your account has been created');
        $this->dispatcher->dispatch(new SecurityRegistrationEvent($user, $token));

        return $user;
    }
}