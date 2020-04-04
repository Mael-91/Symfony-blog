<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogRepository;
use App\Service\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;

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
    /**
     * @var CacheService
     */
    private $cache;
    /**
     * @var AdapterInterface
     */
    private $adapter;
    /**
     * @var CacheInterface
     */
    private $cacheInterface;

    public function __construct(
        BlogRepository $postRepository,
        BlogCategoryRepository $categoryRepository,
        BlogCommentRepository $commentRepository,
        EntityManagerInterface $manager,
        CacheService $cache,
        AdapterInterface $adapter,
        CacheInterface $cacheInterface) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->manager = $manager;
        $this->cache = $cache;
        $this->adapter = $adapter;
        $this->cacheInterface = $cacheInterface;
    }

    /**
     * Liste les articles
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $post = $paginator->paginate($this->postRepository->findAllVisibleQuery(),
            $request->query->getInt('page', 1), 20);
        $category = $this->categoryRepository->findAll();
        $keyPost = $this->postRepository->countPost();
        $keyCategory = $this->categoryRepository->countCategory();
        $post = $this->cache->setCache($keyPost, $post);
        $category = $this->cache->setCache($keyCategory, $category);
        return $this->render('blog/blog.index.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'posts' => $post,
            'categories' => $category,
        ]);
    }

    /**
     * Liste les articles dans les catégories
     * @param BlogCategory $category
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function categoryIndex(BlogCategory $category, PaginatorInterface $paginator, Request $request): Response {
        $posts = $paginator->paginate($this->postRepository->findBy(['category' => $category->getId()]),
            $request->query->getInt('page', '1'), 20);

        return $this->render('blog/category.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'posts' => $posts
        ]);
    }

    /**
     * Affiche un article
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Blog $post
     * @param string $slug
     * @return Response
     * @throws \Exception
     */
    public function show(Request $request, PaginatorInterface $paginator, Blog $post, string $slug): Response {
        $getSlug = $post->getSlug();
        $category = $this->categoryRepository->findOneBy(['id' => $post->getCategory()]);
        $post = $this->cache->setCache($post->getEditedAt()->getTimestamp(), $post);
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.show', [
                'id' => $post->getId(),
                'slug' => $getSlug
            ], 301);
        }
        $comments = $paginator->paginate($this->commentRepository->findBy(['post' => $post, 'visible' => true, 'parent' => null], ['created_at' => 'DESC']),
            $request->query->getInt('page', '1'), 10);
        $nbrCommentInPost = $this->commentRepository->count(['post' => $post]);

        return $this->render('blog/blog.show.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'post' => $post,
            'category' => $category,
            'comments' => $comments,
            'nbrComment' => $nbrCommentInPost
        ]);
    }
}