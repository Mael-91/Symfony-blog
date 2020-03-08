<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController {

    /**
     * @var BlogRepository
     */
    private $postRepository;
    /**
     * @var BlogCategoryRepository
     */
    private $categoryRepository;

    public function __construct(BlogRepository $postRepository, BlogCategoryRepository $categoryRepository) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response {
        $post = $this->postRepository->findLatest();
        $category = $this->categoryRepository->findAll();
        return $this->render('pages/blog/blog.index.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'posts' => $post,
            'categories' => $category
        ]);
    }

    /**
     * @param Blog $post
     * @param string $slug
     * @return Response
     */
    public function show(Blog $post, string $slug): Response {
        $getSlug = $post->getSlug();
        $category = $this->postRepository->findWithCategory($post->getId());
        if ($getSlug !== $slug) {;
            return $this->redirectToRoute('blog.show', [
                'id' => $post->getId(),
                'slug' => $getSlug
            ], 301);
        }
        return $this->render('pages/blog/blog.show.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'post' => $post,
            'category' => $category
        ]);
    }

    public function categoryIndex(BlogCategory $category, string $slug): Response {
        $getSlug = $category->getSlug();
        $posts = $this->postRepository->findPostsInCategory($category->getId());
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.category', [
                'slug' => $getSlug
            ], 301);
        }
        return $this->render('pages/blog/category.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'category' => $category,
            'posts' => $posts
        ]);
    }
}