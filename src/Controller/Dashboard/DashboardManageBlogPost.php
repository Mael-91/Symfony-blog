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
    private $manager;

    public function __construct(BlogRepository $blogRepository, EntityManagerInterface $manager) {
        $this->blogRepository = $blogRepository;
        $this->manager = $manager;
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
            $this->manager->persist($blog);
            $this->manager->flush();
            $this->addFlash('success-blog', 'La publication est un succès !');
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
            $this->manager->flush();
            $this->addFlash('success-blog', 'La modification est un succès !');
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
            $this->manager->remove($blog);
            $this->manager->flush();
            $this->addFlash('success-blog', 'La suppression est un succès !');
        }
        return $this->redirectToRoute('admin.blog.manage.post');
    }
}