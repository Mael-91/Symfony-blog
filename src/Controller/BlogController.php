<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\BlogComment;
use App\Form\BlogCommentType;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    /**
     * @var BlogCommentRepository
     */
    private $commentRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(BlogRepository $postRepository, BlogCategoryRepository $categoryRepository, BlogCommentRepository $commentRepository, EntityManagerInterface $manager) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->manager = $manager;
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
            'categories' => $category,
        ]);
    }

    /**
     * @param Request $request
     * @param Blog $post
     * @param string $slug
     * @return Response
     * @throws \Exception
     */
    public function show(Request $request, Blog $post, string $slug): Response {
        $getSlug = $post->getSlug();
        $category = $this->postRepository->findWithCategory($post->getId());
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.show', [
                'id' => $post->getId(),
                'slug' => $getSlug
            ], 301);
        }
        $comments = $this->commentRepository->findCommentForPost($post->getId());
        $nbrCommentInPost = $this->commentRepository->countCommentInPost($post->getId());

        $comment = new BlogComment();
        $commentForm = $this->createForm(BlogCommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $comment->setPost($post);
                $comment->setAuthor($this->getUser()->getUsername());
                $comment->setCreatedAt(new \DateTime());
                $comment->setVisible(true);
                $addCommentInPost = $post->getNbrComments();
                $post->setNbrComments($addCommentInPost + 1);
                $this->manager->persist($comment);
                $this->manager->flush();
                $success = $this->addFlash('success-send-comment', 'Bravo ! Votre commentaire a été envoyé');
                return $this->redirectToRoute('blog.show', [
                    'id' => $post->getId(),
                    'slug' => $slug,
                    'success-send-comment' => $success
                ], 301);
            }
        }

        return $this->render('pages/blog/blog.show.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'post' => $post,
            'category' => $category,
            'comment' => $comments,
            'commentForm' => $commentForm->createView(),
            'nbrComment' => $nbrCommentInPost
        ]);
    }

    /**
     * @param BlogCategory $category
     * @param string $slug
     * @return Response
     */
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