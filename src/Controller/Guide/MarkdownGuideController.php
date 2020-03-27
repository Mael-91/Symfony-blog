<?php

namespace App\Controller\Guide;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MarkdownGuideController extends AbstractController {

    public function index(): Response {
        return $this->render('guide/markdown.html.twig', [
            'current_menu' => 'guide',
            'is_dashboard' => 'false'
        ]);
    }

}