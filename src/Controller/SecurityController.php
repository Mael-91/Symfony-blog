<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {

    public function login(AuthenticationUtils $authenticationUtils) {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new \Exception('', 403);
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('pages/security/login.html.twig', [
            'current_menu' => 'login',
            'is_dashboard' => 'false',
            'las_username' => $lastUsername,
            'error' => $error
        ]);
    }

}