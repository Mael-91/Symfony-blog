<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\BlogComment;
use App\Entity\BlogLike;
use App\Form\BlogCommentType;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogLikeRepository;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $post = $paginator->paginate($this->postRepository->findAllActiveQuery(),
            $request->query->getInt('page', 1), 12);
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
        $comments = $this->commentRepository->findAllActive($post->getId());
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

    /**
     * Permet de liker ou d'unliker un article
     *
     * @param Blog $post
     * @param BlogLikeRepository $likeRepository
     * @return Response
     */
    public function like(Blog $post, BlogLikeRepository $likeRepository): Response {
        $user = $this->getUser();
        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Vous devez être connecté pour aimer un poste'
        ], 403);
        if ($post->isLikedByUser($user)) {
            $like = $likeRepository->findOneBy([
                'post' => $post,
                'user' => $user
            ]);
            $this->manager->remove($like);
            $this->manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'like supprimé avec succès',
                'likes' => $likeRepository->count(['post' => $post])
            ], 200);
        }
        $like = new BlogLike();
        $like->setPost($post)
            ->setUser($user);
        $this->manager->persist($like);
        $this->manager->flush();
        return $this->json([
            'code' => 200,
            'message' => 'like ajouté',
            'likes' => $likeRepository->count(['post' => $post])
        ], 200);
    }
}