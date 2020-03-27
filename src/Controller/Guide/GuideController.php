<?php

namespace App\Controller\Guide;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GuideController extends AbstractController {

    public function index(): Response {
        return $this->render('guide/guide.html.twig', [
            'current_menu' => 'guide',
            'is_dashboard' => 'false'
        ]);
    }

}