<?php

namespace App\Controller\Core\Like;

use App\Entity\Blog;
use App\Entity\BlogLike;
use App\Event\UserActivityEvent;
use App\Repository\BlogLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlogLikeController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EntityManagerInterface $manager, EventDispatcherInterface $dispatcher) {
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Permet d'aimer un article
     * @param Blog $post
     * @param BlogLikeRepository $repository
     * @return JsonResponse
     */
    public function like(Blog $post, BlogLikeRepository $repository): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => 'You must be logged in to like'
            ], 403);
        }
        if ($post->isLikedByUser($user)) {
            $like = $repository->findOneBy(['post' => $post, 'user' => $user]);
            $this->manager->remove($like);
            $this->manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Removing like => success',
                'likes' => $repository->count(['post' => $post])
            ], 200);
        }
        $like = new BlogLike();
        $like->setPost($post)
            ->setUser($user);
        $this->manager->persist($like);
        $this->manager->flush();
        $this->dispatcher->dispatch(new UserActivityEvent($user, null, null, $like));
        return $this->json([
            'code' => 200,
            'message' => 'Like added',
            'likes' => $repository->count(['post' => $post])
        ], 200);
    }
}