<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardBlogController extends AbstractController {

    /**
     * @return Response
     */
    public function index(): Response {
        return $this->render('pages/dashboard/blog/dashboard_blog.html.twig', [
            'current_menu' => 'dashboard-blog',
            'is_dashboard' => 'true'
        ]);
    }

}