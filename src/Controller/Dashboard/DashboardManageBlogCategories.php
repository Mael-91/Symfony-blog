<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardManageBlogCategories extends AbstractController {

    /**
     * @return Response
     */
    public function index(): Response {
        return $this->render('pages/dashboard/blog/categories.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true'
        ]);
    }

}