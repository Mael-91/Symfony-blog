<?php

namespace App\Controller\Dashboard\Blog;

use App\Entity\BlogComment;
use App\Form\BlogCommentEditType;
use App\Repository\BlogCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function comments(): Response {
        $comments = $this->commentRepository->findAll();
        return $this->render('pages/dashboard/blog/comments.html.twig', [
            'current_menu' => 'blog-comments-manage',
            'is_dashboard' => 'true',
            'comments' => $comments
        ]);
    }

    public function edit(Request $request, BlogComment $comment): Response {
        $commentForm = $this->createForm(BlogCommentEditType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setEditedAt(new \DateTime());
            $this->manager->flush();
            $success = $this->addFlash('success-edit-comment', 'Le commentaire a bien été modifié');
            return $this->redirectToRoute('admin.blog.manage.comment', ['success-edit-comment' => $success]);
        }
        return $this->render('pages/dashboard/blog/crud_comments/edit.html.twig', [
            'current_menu' => 'blog-comments-manage',
            'is_dashboard' => 'true',
            'commentForm' => $commentForm->createView()
        ]);
    }

    public function delete(BlogComment $comment, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token'))) {
            $this->manager->remove($comment);
            $this->manager->flush();
            $this->addFlash('success-delete-comment', 'La suppression est un succès');
        }
        return $this->redirectToRoute('admin.blog.manage.comment');
    }
}