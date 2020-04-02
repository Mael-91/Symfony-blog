<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Repository\PasswordTokenRepository;
use App\Service\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ChangePassword
 * @package App\Controller\Profil
 */

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

    public function __construct(
        EntityManagerInterface $manager,
        PasswordTokenRepository $tokenRepository,
        PasswordService $service,
        UserPasswordEncoderInterface $encoder) {
        $this->manager = $manager;
        $this->tokenRepository = $tokenRepository;
        $this->service = $service;
        $this->encoder = $encoder;
    }

    public function change(Request $request, User $user): Response {
        $token = $this->tokenRepository->findOneBy(['user' => $user]);
        if (!$token || $this->getUser() !== $token->getUser() || $this->service->isExpired($token)) {
            $this->addFlash('error', 'This token has expired'); // TODO Afficher l'erreur sur les pages de profil / login
            if (!$this->getUser()) {
                $this->addFlash('error', 'You should be connected to change your password'); // TODO Afficher l'erreur sur la page de login
                return $this->redirectToRoute('login');
            }
            return $this->redirectToRoute('profil.index', ['id' => $user->getId()]);
        }
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->updatePassword($user, $token);
            $this->addFlash('success', 'Password changed successfully');
            return $this->redirectToRoute('profil.index');
        }
        return $this->render('security/change_password.html.twig', [
            'current_menu' => 'profil-security',
            'is_dashboard' => 'false',
            'changeForm' => $form->createView()
        ]);
    }
}