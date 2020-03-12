<?php

namespace App\Controller;

use App\Component\Mail\MailerComponent;
use App\Entity\User;
use App\Exceptions\AccountTokenExpiredException;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
     * @var MailerComponent
     */
    private $mailerComponent;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $manager, AuthenticationUtils $authenticationUtils, MailerComponent $mailerComponent, TokenGenerator $tokenGenerator) {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->mailerComponent = $mailerComponent;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Permet de s'inscrire
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response {
        if ($this->getUser()) {
            $error = $this->addFlash('error-is-connected', 'Your are already connected');
            return $this->redirectToRoute('home', [ 'error-is-connected' => $error ], 301);
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $user->setRequestedTokenAt(new \DateTime('now'));
            $user->setEnabled(false);
            $user->setCreatedAt(new \DateTime('now'));
            $this->manager->persist($user);
            $this->manager->flush();
            $token = $user->getConfirmationToken();
            $email = $user->getEmail();
            $id = $user->getId();
            $user = $user->getUsername();
            $this->mailerComponent->sendRegisterMail($user, $email, $id, $token, 'confirmation_account');
            $success = $this->addFlash('success-register', 'Bravo, votre compte a été crée !');
            return $this->redirectToRoute('home', [ 'success' => $success ], 301);
        }

        return $this->render('pages/security/signup.html.twig', [
            'current_menu' => 'sign-up',
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
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
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
     * @param $token
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function confirmAccount($token, $id): Response {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (is_null($user->getConfirmationToken()) || $token !== $user->getConfirmationToken() || !$this->isRequestInTime($user->getRequestedTokenAt())) {
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

    /*public function changePassword() {

    }*/

    /**
     * Permet de vérifier le temps depuis la génération du token
     *
     * @param \DateTime|null $requestedAt
     * @return bool
     * @throws \Exception
     */
    private function isRequestInTime(\DateTime $requestedAt = null): bool {
        if (is_null($requestedAt)) {
            throw new AccountTokenExpiredException();
        }

        $now = new \DateTime('now');
        $interval = $now->getTimestamp() - $requestedAt->getTimestamp();
        $validTime = 60 * 15;
        $isValid = $interval > $validTime ? false : $isValid = true;
        return $isValid;
    }
}