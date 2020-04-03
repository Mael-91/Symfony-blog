<?php

namespace App\Controller\Admin\Blog;

use App\Entity\BlogComment;
use App\Form\BlogCommentEditType;
use App\Repository\BlogCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogCommentsController extends AbstractController {

    /**
     * @var BlogCommentRepository
     */
    private $commentRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(BlogCommentRepository $commentRepository, EntityManagerInterface $manager) {

        $this->commentRepository = $commentRepository;
        $this->manager = $manager;
    }

    /**
     * Permet d'afficher tout les commentaires des articles
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function comments(PaginatorInterface $paginator, Request $request): Response {
        $comments = $paginator->paginate($this->commentRepository->findAll(),
            $request->query->getInt('page', '1'), 20);
        return $this->render('admin/blog/comments.html.twig', [
            'current_menu' => 'blog-comments-manage',
            'is_dashboard' => 'true',
            'comments' => $comments
        ]);
    }

    /**
     * Permet d'éditer un commentaire
     *
     * @param Request $request
     * @param BlogComment $comment
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, BlogComment $comment): Response {
        $commentForm = $this->createForm(BlogCommentEditType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setEditedAt(new \DateTime());
            $this->manager->flush();
            $this->addFlash('success', 'Le commentaire a bien été modifié');
            return $this->redirectToRoute('admin.blog.manage.comment', [], 301);
        }
        return $this->render('admin/blog/crud_comments/edit.html.twig', [
            'current_menu' => 'blog-comments-manage',
            'is_dashboard' => 'true',
            'commentForm' => $commentForm->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @param BlogComment $comment
     * @param Request $request
     * @return Response
     */
    public function delete(BlogComment $comment, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token'))) {
            $this->manager->remove($comment);
            $this->manager->flush();
            $this->addFlash('success', 'La suppression est un succès');
        }
        return $this->redirectToRoute('admin.blog.manage.comment');
    }
}