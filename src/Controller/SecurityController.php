<?php

namespace App\Controller;

use App\Entity\ConfirmationToken;
use App\Entity\User;
use App\Event\GenerateAvatarEvent;
use App\Event\LoginAfterRegistrationEvent;
use App\Event\SecurityLoginEvent;
use App\Event\SecurityRegistrationEvent;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SecurityController extends AbstractController {

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var TokenGeneratorService
     */
    private $tokenGenerator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        AuthenticationUtils $authenticationUtils,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorService $tokenGenerator,
        EventDispatcherInterface $dispatcher) {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Permet de s'inscrire
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request): Response {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') ||
            $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $error = $this->addFlash('error', 'Your are already connected');
            return $this->redirectToRoute('home', [ 'error-is-connected' => $error ], 301);
        }

        $user = new User();
        $token = new ConfirmationToken();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()))
                ->setRoles([User::DEFAULT_ROLE]);
            $this->manager->persist($user);
            $token->setUser($user)
                ->setToken($this->tokenGenerator->generateToken(10));
            $this->manager->persist($token);
            $this->manager->flush();
            $this->addFlash('success', 'Well done, your account has been created !'); // Mettre erreur spéciale dans le _success_alert (voir carte -> Frontend)
            $this->dispatcher->dispatch(new SecurityRegistrationEvent($user, $token));
            $this->dispatcher->dispatch(new LoginAfterRegistrationEvent($user));
            $this->dispatcher->dispatch(new GenerateAvatarEvent($user));
            return $this->redirectToRoute('home', [], Response::HTTP_FOUND);
        }

        return $this->render('security/signup.html.twig', [
            'current_menu' => 'register',
            'is_dashboard' => 'false',
            'user' => $user,
            'registration' => $form->createView()
        ]);
    }

    /**
     * Permet de se connecter avec un utilisateur
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
         if ($this->isGranted('IS_AUTHENTICATED_FULLY') ||
             $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
             $this->addFlash('error', 'Your are already connected');
             return $this->redirectToRoute('home', [], 301);
         }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'current_menu' => 'login',
            'is_dashboard' => 'false',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function logout(): void {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}