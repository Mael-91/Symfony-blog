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

class GithubAuthController extends AbstractController {

    private $githubId;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var MailerComponent
     */
    private $mailerComponent;

    public function __construct($githubId, EntityManagerInterface $manager, SessionInterface $session, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $passwordEncoder, UrlGeneratorInterface $urlGenerator, MailerComponent $mailerComponent)
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
        $this->mailerComponent = $mailerComponent;
        $this->githubId = $githubId;
    }

    /**
     * @inheritDoc
     */
    public function connect(): Response {
        $url = $this->urlGenerator->generate('github_connect', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $state = $this->session->set('oauth-github-state', base64_encode(random_bytes(50)));
        return new RedirectResponse("https://github.com/login/oauth/authorize?client_id=$this->githubId&redirect_uri=$url&state=$state");
    }

    /**
     * @inheritDoc
     */
    public function generateAccount(string $username, string $email) {
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