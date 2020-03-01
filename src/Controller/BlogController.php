<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController {

    /**
     * @return Response
     */
    public function index(): Response {
        return $this->render('pages/blog/blog.index.html.twig', [
            'current_menu' => 'blog'
        ]);
    }
}