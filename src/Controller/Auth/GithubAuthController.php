<?php

namespace App\Controller\Auth;

use App\Component\Mail\MailerComponent;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GithubAuthController extends AbstractController {

    private $githubId;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var MailerComponent
     */
    private $mailerComponent;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct($githubId, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator, MailerComponent $mailerComponent, EntityManagerInterface $manager, SessionInterface $session) {
        $this->githubId = $githubId;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailerComponent = $mailerComponent;
        $this->manager = $manager;
        $this->session = $session;
    }

    public function connect() {
        $url = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $state = $this->session->set('oauth', base64_encode(random_bytes(50)));
        return new RedirectResponse("https://github.com/login/oauth/authorize?client_id=$this->githubId&redirect_uri=$url&state=$state");
    }

    public function generateAccount($username, $email) {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $password = $this->passwordEncoder->encodePassword($user, random_bytes(20));
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER']);
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setRequestedTokenAt(new \DateTime());
        $user->setCreatedAt(new \DateTime());
        $user->setEnabled(false);
        $this->manager->persist($user);
        $this->manager->flush();
        $username = $user->getUsername();
        $id = $user->getId();
        $email = $user->getEmail();
        $token = $user->getConfirmationToken();
        $this->mailerComponent->sendRegisterMail($username, $email, $id, $token, 'confirmation_account');

        return $user;
    }
}