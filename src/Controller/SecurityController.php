<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\SecurityForgotPasswordRequestEvent;
use App\Event\SecurityLoginEvent;
use App\Event\SecurityPasswordInformationEvent;
use App\Event\SecurityRegistrationEvent;
use App\EventSubscriber\MailSubscriber;
use App\Form\ForgotPasswordType;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Form\ResettingPasswordType;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $manager, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator) {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Permet de s'inscrire
     *
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, EventDispatcherInterface $dispatcher): Response {
        if ($this->getUser()) {
            $error = $this->addFlash('error-is-connected', 'Your are already connected');
            return $this->redirectToRoute('home', [ 'error-is-connected' => $error ], 301);
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $user->setRequestedTokenAt(new \DateTime('now'));
            $user->setEnabled(false);
            $user->setCreatedAt(new \DateTime('now'));
            $this->manager->persist($user);
            $this->manager->flush();
            $success = $this->addFlash('success-register', 'Bravo, votre compte a été crée !');
            $registrationEvent = new SecurityRegistrationEvent($user);
            $dispatcher->dispatch($registrationEvent, SecurityRegistrationEvent::NAME);
            // Login user after registration
            $loginAfter = new UsernamePasswordToken($user->getUsername(), $user->getPassword(), 'security.user.provider.concrete.from_database', $user->getRoles());
            $this->get('security.token_storage')->setToken($loginAfter);
            $this->get('session')->set('_security_main', serialize($loginAfter));
            return $this->redirectToRoute('home', [ 'success' => $success ], 301);
        }

        return $this->render('pages/security/signup.html.twig', [
            'current_menu' => 'register',
            'is_dashboard' => 'false',
            'user' => $user,
            'registration' => $form->createView()
        ]);
    }

    /**
     * Permet de se connecter avec un utilisateur
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             $error = $this->addFlash('error-is-connected', 'Your are already connected');
             return $this->redirectToRoute('home', [ 'error-login' => $error ], 301);
         }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('pages/security/login.html.twig', [
            'current_menu' => 'login',
            'is_dashboard' => 'false',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Permet de confirmer le compte
     *
     * @param string $token
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function confirmAccount($token, $id): Response {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (is_null($user->getConfirmationToken()) || $token !== $user->getConfirmationToken() || !$this->tokenGenerator->isRequestInTime($user->getRequestedTokenAt())) {
            throw new AccessDeniedHttpException('Un problème est survenu lors de la demande de vérification du compte, veuillez rééssayer');
        } else {
            $user->setConfirmationToken(null);
            $user->setRequestedTokenAt(null);
            $user->setEnabled(true);
            $this->manager->flush();
            $successConfirmation = $this->addFlash('success-confirm-account', 'Votre compte a bien été confirmé');
            return $this->redirectToRoute('home', ['success-confirmation' => $successConfirmation], 301);
        }
    }

    /**
     * Permet d'emettre une demande de changement du mot de passe
     *
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     * @throws \Exception
     */
    public function forgotPasswordRequest(Request $request, EventDispatcherInterface $dispatcher): Response {
        if ($this->getUser()) {
            throw new AccessDeniedHttpException('Vous n\'avez pas le droit d\'acceder à cette page lorsque vous êtes connecter');
        }

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $email = $form->getData();
            $findUser = $this->userRepository->findOneBy(['email' => $email->getEmail()]);
            if (is_null($findUser)) {
                $emailNotExist = $this->addFlash('error-email-not-exist', 'L\'email spécifié n\'existe pas');
                return $this->redirectToRoute('forgot_password_request', ['error-email-exist' => $emailNotExist], 301);
            } elseif ($form->isSubmitted() && $form->isValid() && $findUser !== null) {
                $findUser->setPasswordToken($this->tokenGenerator->generateToken(10));
                $findUser->setRequestedPwTokenAt(new \DateTime('now'));
                $this->manager->flush();
                $passwordEvent = new SecurityForgotPasswordRequestEvent($findUser);
                $dispatcher->dispatch($passwordEvent, SecurityForgotPasswordRequestEvent::NAME);
                $successSendMail = $this->addFlash('success-send-mail-fopw', 'Le mail a bien été envoyé');

                return $this->redirectToRoute('login', ['success-mail-fopw' => $successSendMail], 301);
            }
        }

        return $this->render('pages/security/forgot_password.html.twig', [
            'is_dashboard' => 'false',
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'effectuer le changement de mot de passe
     *
     * @param Request $request
     * @param string $token
     * @param EventDispatcherInterface $dispatcher
     * @return Response
     * @throws \Exception
     */
    public function forgotPasswordPost(Request $request, $token, EventDispatcherInterface $dispatcher): Response {
        $user = $this->userRepository->findOneBy(['password_token' => $token]);
        $form = $this->createForm(ResettingPasswordType::class);
        $form->handleRequest($request);
        if (is_null($user->getPasswordToken()) || $token !== $user->getPasswordToken() || !$this->tokenGenerator->isRequestInTime($user->getRequestedPwTokenAt())) {
            throw new AccessDeniedHttpException('Un problème est survenu lors de la demande de changement du mot de passe, veuillez réitérer la demande');
        } else {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $password = $this->passwordEncoder->encodePassword($user, $data->getPassword());
                $user->setPassword($password);
                $user->setPasswordToken(null);
                $user->setRequestedPwTokenAt(null);
                $this->manager->flush();
                $resetConfirmation = new SecurityPasswordInformationEvent($user);
                $dispatcher->dispatch($resetConfirmation, SecurityPasswordInformationEvent::NAME);
                $successChangePW = $this->addFlash('success-change-password', 'Le mot de passe a bien été modifié');
                return $this->redirectToRoute('login', ['success-change-pw' => $successChangePW], 301);
            }
        }

        return $this->render('pages/security/change_password.html.twig', [
            'is_dashboard' => 'false',
            'form' => $form->createView()
        ]);
    }
}