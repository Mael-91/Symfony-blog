<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController {

    /**
     * @var BlogRepository
     */
    private $blogRepository;

    public function __construct(BlogRepository $blogRepository) {
        $this->blogRepository = $blogRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response {
        $post = $this->blogRepository->findLatest();
        return $this->render('pages/blog/blog.index.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'posts' => $post
        ]);
    }

    /**
     * @param Blog $post
     * @param string $slug
     * @return Response
     */
    public function show(Blog $post, string $slug): Response {
        $getSlug = $post->getSlug();
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.show', [
                'id' => $post->getId(),
                'slug' => $getSlug
            ], 301);
        }
        return $this->render('pages/blog/blog.show.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'post' => $post
        ]);
    }
}