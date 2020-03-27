<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\BlogComment;
use App\Entity\BlogLike;
use App\Exceptions\UserNotConnectedException;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogLikeRepository;
use App\Repository\BlogReplyRepository;
use App\Repository\BlogRepository;
use App\Service\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $post = $paginator->paginate($this->postRepository->findAllActiveQuery(),
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
     * @param BlogCategory $category
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function categoryIndex(BlogCategory $category, PaginatorInterface $paginator, Request $request, string $slug): Response {
        $getSlug = $category->getSlug();
        $posts = $paginator->paginate($this->postRepository->findPostsInCategory($category->getId()),
            $request->query->getInt('page', '1'), 20);
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.category', [
                'slug' => $getSlug
            ], 301);
        }

        return $this->render('blog/category.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'category' => $category,
            'posts' => $posts
        ]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Blog $post
     * @param string $slug
     * @return Response
     * @throws \Exception
     */
    public function show(Request $request, PaginatorInterface $paginator, Blog $post, string $slug): Response {
        $getSlug = $post->getSlug();
        $category = $this->postRepository->findWithCategory($post->getId());
        $post = $this->cache->setCache($post->getEditedAt()->getTimestamp(), $post);
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.show', [
                'id' => $post->getId(),
                'slug' => $getSlug
            ], 301);
        }
        $comments = $paginator->paginate($this->commentRepository->findBy(['post' => $post, 'visible' => true, 'parent' => null], ['created_at' => 'DESC']),
            $request->query->getInt('page', '1'), 20);
        $comments = $this->cache->setCache('10', $comments);
        $nbrCommentInPost = $this->commentRepository->count(['post' => $post]);
        $nbrCommentInPost = $this->cache->setCache($nbrCommentInPost, $nbrCommentInPost);

        return $this->render('blog/blog.show.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'post' => $post,
            'category' => $category,
            'comments' => $comments,
            'nbrComment' => $nbrCommentInPost
        ]);
    }

    /**
     * REFACTORING :
     * Passer les méthodes comment & reply dans un controller a part
     * et dans une seule méthode récupérant l'id du commentaire via un $request->query
     */

    /**
     * @param Request $request
     * @param Blog $post
     * @return Response
     * @throws \Exception
     */
    public function comment(Request $request, Blog $post): Response {
        $content = $request->request->get('comment_zone');
        $user = $this->getUser();
        $comment = new BlogComment();
        if (!$user) {
           throw new UserNotConnectedException();
        }
        if (empty($comment)) {
            throw new \Exception('Le contenu ne peut être vide');
        }
        if (!$this->isCsrfTokenValid('comment' . $post->getId(), $request->request->get('_csrf_token_comment'))) {
            throw new InvalidCsrfTokenException();
        }
        $comment->setPost($post)
            ->setAuthor($this->getUser())
            ->setContent($content)
            ->setCreatedAt(new \DateTime())
            ->setVisible(true);
        $this->manager->persist($comment);
        $this->manager->flush();
        $this->addFlash('success-send-comment', 'Bravo ! Votre commentaire a été envoyé');
        return new JsonResponse(['code' => 200, 'message' => 'commentaire envoyé avec succès'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param Blog $post
     * @param BlogComment $comment
     * @return Response
     * @throws \Exception
     * @ParamConverter("comment", class="App\Entity\BlogComment")
     */
    public function reply(Request $request, Blog $post, BlogComment $comment): Response {
        $content = $request->request->get('reply_zone');
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('You must be logged in to comment');
        }
        if (empty($content)) {
            throw new BadRequestHttpException('The content must not be null');
        }
        if (!$this->isCsrfTokenValid('reply_comment' . $comment->getId(), $request->request->get('_csrf_token_reply'))) {
            throw new InvalidCsrfTokenException();
        }
        $reply = new BlogComment();
        $reply->setAuthor($this->getUser())
            ->setPost($post)
            ->setContent($content)
            ->setCreatedAt(new \DateTime())
            ->setVisible(true)
            ->setParent($comment);
        $this->manager->persist($reply);
        $this->manager->flush();
        //$success = $this->addFlash('success-send-comment', 'Bravo ! Votre réponse a été envoyé');
        return new JsonResponse(['code' => 200, 'message' => 'réponse envoyé avec succès'], JsonResponse::HTTP_CREATED);
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