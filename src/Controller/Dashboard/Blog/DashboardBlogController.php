<?php

namespace App\Controller\Dashboard\Blog;

use App\Repository\BlogCategoryRepository;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardBlogController extends AbstractController {

    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var BlogCategoryRepository
     */
    private $categoryRepository;

    public function __construct(BlogRepository $blogRepository, BlogCategoryRepository $categoryRepository) {
        $this->blogRepository = $blogRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response {
        $numPost = $this->blogRepository->countPost();
        $numCategory = $this->categoryRepository->countCategory();
        return $this->render('pages/dashboard/blog/dashboard_blog.html.twig', [
            'current_menu' => 'dashboard-blog',
            'is_dashboard' => 'true',
            'numbersPost' => $numPost,
            'numbersCategory' => $numCategory
        ]);
    }

}