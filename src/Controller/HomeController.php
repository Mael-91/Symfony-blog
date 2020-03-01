<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController {

    /**
     * @return Response
     */
    public function index(): Response {
        return $this->render('pages/home.html.twig', [
            'current_menu' => 'home',
            'is_dashboard' => 'false'
        ]);
    }
}