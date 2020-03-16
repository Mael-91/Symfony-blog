<?php

namespace App\Controller\Auth;

use App\Component\Mail\MailerComponent;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @var MailerComponent
     */
    private $mailerComponent;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct($twitchId, UrlGeneratorInterface $urlGenerator, SessionInterface $session, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, MailerComponent $mailerComponent, TokenGenerator $tokenGenerator) {

        $this->twitchId = $twitchId;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->mailerComponent = $mailerComponent;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function connect() {
        $url = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
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
        $this->manager->persist($user);
        $this->manager->flush();
        $id = $user->getId();
        $token = $user->getConfirmationToken();
        $this->mailerComponent->sendRegisterMail($username, $email, $id, $token);

        return $user;
    }
}