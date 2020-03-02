<?php

namespace App\Controller\Dashboard;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardManageBlogPost extends AbstractController {

    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(BlogRepository $blogRepository, EntityManagerInterface $objectManager) {
        $this->blogRepository = $blogRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @return Response
     */
    public function index(): Response {
        $posts = $this->blogRepository->findAll();
        return $this->render('pages/dashboard/blog/posts.html.twig', [
            'current_menu' => 'blog-posts-manage',
            'is_dashboard' => 'true',
            'posts' => $posts
        ]);
    }

    public function create(Request $request): Response {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->objectManager->persist($blog);
            $this->objectManager->flush();
            $this->addFlash('success', 'La publication est un succès !');
            return $this->redirectToRoute('admin.blog.manage.post');
        }
        return $this->render('pages/dashboard/blog/crud_posts/create.html.twig', [
            'current_menu' => 'blog-posts-manage',
            'is_dashboard' => 'true',
            'blog' => $blog,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Blog $blog
     * @param Request $request
     * @return Response
     */
    public function edit(Blog $blog, Request $request): Response {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->objectManager->flush();
            $this->addFlash('success', 'La modification est un succès !');
            return $this->redirectToRoute('admin.blog.manage.post');
        }
        return $this->render('pages/dashboard/blog/crud_posts/edit.html.twig', [
            'current_menu' => 'blog-posts-manage',
            'is_dashboard' => 'true',
            'blog' => $blog,
            'form' => $form->createView()
        ]);
    }

    public function delete(Blog $blog, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->get('_token'))) {
            $this->objectManager->remove($blog);
            $this->objectManager->flush();
            $this->addFlash('success', 'La suppression est un succès !');
        }
        return $this->redirectToRoute('admin.blog.manage.post');
    }
}