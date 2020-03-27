<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController {

    /**
     * @return Response
     */
    public function dashboard(): Response {
        return $this->render('admin/dashboard.html.twig', [
            'current_menu' => 'dashboard',
            'is_dashboard' => 'true'
        ]);
    }
}