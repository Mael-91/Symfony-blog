<?php

namespace App\Controller\Dashboard;

use App\Entity\BlogCategory;
use App\Form\BlogCategoryType;
use App\Repository\BlogCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardBlogCategoriesController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var BlogRepository
     */
    private $categoryRepository;

    public function __construct(BlogCategoryRepository $categoryRepository, EntityManagerInterface $manager) {
        $this->manager = $manager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response {
        $categories = $this->categoryRepository->findAll();
        return $this->render('pages/dashboard/blog/categories.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true',
            'categories' => $categories
        ]);
    }

    public function create(Request $request): Response {
        $category = new BlogCategory();
        $form = $this->createForm(BlogCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();
            $success = $this->addFlash('success-create-category', 'La catégorie a bien été créée');
            return $this->redirectToRoute('admin.blog.manage.categories', ['success-create' => $success], 301);
        }
        return $this->render('pages/dashboard/blog/crud_categories/create.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true',
            'form' => $form->createView()
        ]);
    }

    public function edit(BlogCategory $category, Request $request): Response {
        $form = $this->createForm(BlogCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $success = $this->addFlash('success-edit-category', 'La catégorie a bien été modifiée');
            return $this->redirectToRoute('admin.blog.manage.categories', ['success-edit' => $success], 301);
        }
        return $this->render('pages/dashboard/blog/crud_categories/edit.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true',
            'form' => $form->createView()
        ]);
    }

    public function delete(BlogCategory $category, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token'))) {
            $this->manager->remove($category);
            $this->manager->flush();
            $this->addFlash('success-delete-category', 'La suppression est un succès !');
        }
        return $this->redirectToRoute('admin.blog.manage.categories', [], 301);
    }
}