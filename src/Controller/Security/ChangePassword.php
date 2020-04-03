<?php

namespace App\Controller\Security;

use App\Form\ForgotPasswordType;
use App\Form\ResettingPasswordType;
use App\Repository\PasswordTokenRepository;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ChangePassword extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var PasswordTokenRepository
     */
    private $tokenRepository;
    /**
     * @var PasswordService
     */
    private $service;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $manager,
        PasswordTokenRepository $tokenRepository,
        PasswordService $service,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository) {
        $this->manager = $manager;
        $this->tokenRepository = $tokenRepository;
        $this->service = $service;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    public function forgotPassword(Request $request): Response {
        if ($this->getUser()) {
            throw new AccessDeniedException('You cannot access this page when you are logged in');
        }
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData();
            $user = $this->userRepository->findOneBy(['email' => $email->getEmail()]);
            if ($user) {
                $this->service->resetPassword($user);
                $this->addFlash('success', 'An email has been sent to you to change your password');
                return $this->redirectToRoute('login', [], 301);
            } else {
                $this->addFlash('error', 'Email does not exist');
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'is_dashboard' => 'false',
            'form' => $form->createView()
        ]);
    }

    public function change(Request $request, string $token): Response {
        $token = $this->tokenRepository->findOneBy(['token' => $token]);
        if ($this->getUser()) {
            if (!$token || $this->getUser() !== $token->getUser() || $this->service->isExpired($token)) {
                $this->addFlash('error', 'This token has expired');
                return $this->redirectToRoute('home', [], 301);
            }
        } elseif (!$this->getUser() && is_null($token) || $this->service->isExpired($token)) {
            $this->addFlash('error', 'The token has expired');
            return $this->redirectToRoute('login', [], 301);
        }
        $form = $this->createForm(ResettingPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->updatePassword($form->getData()->getPassword(), $token);
            $this->addFlash('success', 'Your password was successfully changed');
            if ($this->getUser()) {
                return $this->redirectToRoute('home', [], 301);
            }
            return $this->redirectToRoute('login', [], 301);
        }
        return $this->render('security/change_forgot_password.html.twig', [
            'is_dashboard' => 'false',
            'form' => $form->createView()
        ]);
    }
}