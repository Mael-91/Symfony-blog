<?php

namespace App\Controller\Admin;

use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogLikeRepository;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController {

    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var BlogCategoryRepository
     */
    private $categoryRepository;
    /**
     * @var BlogCommentRepository
     */
    private $commentRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var BlogLikeRepository
     */
    private $likeRepository;

    public function __construct(
        BlogRepository $blogRepository,
        BlogCategoryRepository $categoryRepository,
        BlogCommentRepository $commentRepository,
        UserRepository $userRepository,
        BlogLikeRepository $likeRepository) {
        $this->blogRepository = $blogRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
    }

    /**
     * @return Response
     */
    public function dashboard(): Response {
        $nbrPost = $this->blogRepository->countPost();
        $lastPost = $this->blogRepository->findLastArticle();
        $nbrCategory = $this->categoryRepository->countCategory();
        $nbrComment = $this->commentRepository->countComment();
        $lastComment = $this->commentRepository->findLastComment();
        $nbrUser = $this->userRepository->countUser();
        $nbrViaOAuth = $this->userRepository->countUserWithOAuth();
        $lastUser = $this->userRepository->findLastUser();
        $nbrLike = $this->likeRepository->countLike();
        return $this->render('admin/dashboard.html.twig', [
            'current_menu' => 'dashboard',
            'is_dashboard' => 'true',
            'nbrPost' => $nbrPost,
            'lastPost' => $lastPost,
            'nbrCategory' => $nbrCategory,
            'nbrComment' => $nbrComment,
            'nbrUser' => $nbrUser,
            'lastUser' => $lastUser,
            'userOAuth' => $nbrViaOAuth,
            'nbrLike' => $nbrLike,
            'lastComment' => $lastComment,
        ]);
    }
}