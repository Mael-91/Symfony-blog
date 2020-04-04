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
     * @var TokenGeneratorService
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        $githubId,
        EntityManagerInterface $manager,
        SessionInterface $session,
        TokenGeneratorService $tokenGenerator,
        UserPasswordEncoderInterface $passwordEncoder,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $dispatcher)
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
        $this->githubId = $githubId;
        $this->dispatcher = $dispatcher;
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