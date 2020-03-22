<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\BlogComment;
use App\Entity\BlogLike;
use App\Entity\BlogReply;
use App\Exceptions\UserNotConnectedException;
use App\Form\BlogCommentType;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogLikeRepository;
use App\Repository\BlogReplyRepository;
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
    /**
     * @var BlogReplyRepository
     */
    private $replyRepository;

    public function __construct(
        BlogRepository $postRepository,
        BlogCategoryRepository $categoryRepository,
        BlogCommentRepository $commentRepository,
        BlogReplyRepository $replyRepository,
        EntityManagerInterface $manager) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->replyRepository = $replyRepository;
        $this->manager = $manager;
    }

    /**
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $post = $paginator->paginate($this->postRepository->findAllActiveQuery(),
            $request->query->getInt('page', 1), 20);
        $category = $this->categoryRepository->findAll();
        return $this->render('pages/blog/blog.index.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'posts' => $post,
            'categories' => $category,
        ]);
    }

    /**
     * @param BlogCategory $category
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

        return $this->render('pages/blog/category.html.twig', [
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
        if ($getSlug !== $slug) {
            return $this->redirectToRoute('blog.show', [
                'id' => $post->getId(),
                'slug' => $getSlug
            ], 301);
        }
        $comments = $paginator->paginate($this->commentRepository->findAllActive($post->getId()),
            $request->query->getInt('page', '1'), 20);
        $nbrCommentInPost = $this->commentRepository->countCommentInPost($post->getId());

        $comment = new BlogComment();
        $commentForm = $this->createForm(BlogCommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                return $this->comment($comment, $post, $slug);
            }
        }

        //$reply = $this->manager->getRepository(BlogReply::class)->findOneBy(['comment' => '2484']);
        //dd($reply);

        return $this->render('pages/blog/blog.show.html.twig', [
            'current_menu' => 'blog',
            'is_dashboard' => 'false',
            'post' => $post,
            'category' => $category,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'nbrComment' => $nbrCommentInPost,
        ]);
    }

    public function comment(BlogComment $entity, $post, string $slug): Response {
        $entity->setPost($post);
        $entity->setAuthor($this->getUser()->getUsername());
        $entity->setCreatedAt(new \DateTime());
        $entity->setVisible(true);
        $this->manager->persist($entity);
        $this->manager->flush();
        $success = $this->addFlash('success-send-comment', 'Bravo ! Votre commentaire a été envoyé');
        return $this->redirectToRoute('blog.show', [
            'id' => $post->getId(),
            'slug' => $slug,
            'success-send-comment' => $success
        ], 301);
    }

    public function replyComment(Blog $post, BlogComment $comment, Request $request): Response {
        $user = $this->getUser();
        if (!$user) {
            throw new UserNotConnectedException();
        }
        // Faire une vérification avec listener / subscriber une fois l'usercheck implémanté
        // pour vérifier si l'utilisateur n'est pas banni ou restreint
        $content = $request->request->get('reply_zone');
        $parentId = $request->request->get('parent_id');
        $parentExist = $this->commentRepository->findOneBy(['id' => $parentId]);
        if (!$parentExist) {
            throw new \Exception('Parent doesn\'t exist', 400);
        }
        if (empty($content)) {
            throw new \Exception('The content must not be null', 400);
        }
        if (!empty($content) && $token = $this->isCsrfTokenValid('reply_comment' . $comment->getId(), $request->request->get('_csrf_token'))) {
            $reply = new BlogReply();
            $reply->setPost($post)
                ->setComment($comment)
                ->setAuthor($user)
                ->setReplyContent($content)
                ->setCreatedAt(new \DateTime())
                ->setVisible(true);
            $this->manager->persist($reply);
            $this->manager->flush();
            //return $this->json(['code' => 200, 'message' => 'commentaire envoyé avec succès'], 200);
            return $this->redirectToRoute('blog.show', ['id' => $post->getId(), 'slug' => $post->getSlug()], 301);
        }
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