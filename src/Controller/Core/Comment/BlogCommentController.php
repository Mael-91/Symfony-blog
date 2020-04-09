<?php

namespace App\Controller\Core\Comment;

use App\Entity\Blog;
use App\Entity\BlogComment;
use App\Event\UserActivityEvent;
use App\Exceptions\UserNotConnectedException;
use App\Repository\BlogCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @todo Faire les réponses en Json
 */

class BlogCommentController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var BlogCommentRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $manager,
        EventDispatcherInterface $dispatcher,
        BlogCommentRepository $repository) {
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
    }

    /**
     * Permet de commenter un article
     * @param Request $request
     * @param Blog $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function comment(Request $request, Blog $post): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to comment');
            throw new UserNotConnectedException();
        }
        if (empty($data['content'])) {
            //$this->addFlash('error', 'The content must not be null');
            return new JsonResponse(['message' => 'The content must not be null'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!$this->isCsrfTokenValid('comment' . $post->getId(), $data['csrf'])) {
            throw new InvalidCsrfTokenException();
        }
        $comment = new BlogComment();
        $comment->setPost($post)
            ->setAuthor($user)
            ->setContent($data['content']);
        $this->manager->persist($comment);
        $this->manager->flush();
        $this->dispatcher->dispatch(new UserActivityEvent($user, null, $comment, null));
        //$this->addFlash('success', 'Well done ! Your comment has been sent.');
        $getNbrComment = $this->repository->count(['post' => $post]);
        return new JsonResponse([
            'message' => 'commentaire envoyé avec succès',
            'comment' =>  [
                'id' => $comment->getId(),
                'author' => [
                    'id' => $comment->getAuthor()->getId(),
                    'user' => $comment->getAuthor()->getUsername(),
                    'avatar' => $comment->getAuthor()->getAvatarFilename()
                ],
                'date' => $comment->getCreatedAt()->format(\DateTime::ISO8601),
                'content' => $comment->getContent()
            ],
            'nbrComment' => $getNbrComment,
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Permet de répondre à un commentaire
     * @param Request $request
     * @param Blog $post
     * @param BlogComment $comment
     * @return JsonResponse
     * @ParamConverter("comment", class="App\Entity\BlogComment")
     */
    public function reply(Request $request, Blog $post, BlogComment $comment): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to comment');
            throw $this->createAccessDeniedException();
        }
        if (empty($data['content'])) {
            //$this->addFlash('error', 'The content must not be null');
            return new JsonResponse(['message' => 'The content must not be null'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $reply = new BlogComment();
        $reply->setAuthor($user)
            ->setPost($post)
            ->setContent($data['content'])
            ->setParent($comment);
        $this->manager->persist($reply);
        $this->manager->flush();
        $this->dispatcher->dispatch(new UserActivityEvent($user, null, $reply, null));
        //$this->addFlash('success', 'Well done, you reply has been sent.');
        $getNbrComment = $this->repository->count(['post' => $post]);
        return new JsonResponse([
            'message' => 'réponse envoyée avec succès',
            'reply' => [
                'parent' => $comment->getId(),
                'author' => [
                    'id' => $reply->getAuthor()->getId(),
                    'user' => $reply->getAuthor()->getUsername(),
                    'avatar' => $reply->getAuthor()->getAvatarFilename()
                ],
                'date' => $reply->getCreatedAt()->format(\DateTime::ISO8601),
                'content' => $reply->getContent()
            ],
            'nbrComment' => $getNbrComment
        ], JsonResponse::HTTP_CREATED);
    }
}