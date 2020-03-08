<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    public function __construct(UserRepository $userRepository, EntityManagerInterface $manager, AuthenticationUtils $authenticationUtils) {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

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
            $user->setCreatedAt(new \DateTime('now'));
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', 'Bravo, votre compte a été crée !');
            return $this->redirectToRoute('home', [], 301);
        }

        return $this->render('pages/security/signup.html.twig', [
            'current_menu' => 'sign-up',
            'is_dashboard' => 'false',
            'user' => $user,
            'registration' => $form->createView()
        ]);
    }

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

    /*public function changePassword() {

    }*/
}