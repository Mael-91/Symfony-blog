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
     * @var MailerComponent
     */
    private $mailerComponent;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct($googleId, UrlGeneratorInterface $urlGenerator, SessionInterface $session, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, MailerComponent $mailerComponent, TokenGenerator $tokenGenerator) {

        $this->googleId = $googleId;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->mailerComponent = $mailerComponent;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function connect(): Response {
        $url = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $state = $this->session->set('oauth-google', base64_encode(random_bytes(30)));
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
        $user->setCreatedAt(new \DateTime());
        $this->manager->persist($user);
        $this->manager->flush();
        $id = $user->getId();
        $token = $user->getConfirmationToken();
        $this->mailerComponent->sendRegisterMail($username, $email, $id, $token);

        return $user;
    }
}