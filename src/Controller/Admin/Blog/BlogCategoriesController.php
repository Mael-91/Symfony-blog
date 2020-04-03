<?php

namespace App\Controller\Admin\Blog;

use App\Controller\Admin\BlogRepository;
use App\Entity\BlogCategory;
use App\Form\BlogCategoryType;
use App\Repository\BlogCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogCategoriesController extends AbstractController {

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
     * Permet d'afficher toutes les catégories
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $categories = $paginator->paginate($this->categoryRepository->findAll(),
            $request->query->getInt('page', 1), 20);
        return $this->render('admin/blog/categories.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true',
            'categories' => $categories
        ]);
    }

    /**
     * Permet de créer une catégorie
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response {
        $category = new BlogCategory();
        $form = $this->createForm(BlogCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();
            $this->addFlash('success', 'La catégorie a bien été créée');
            return $this->redirectToRoute('admin.blog.manage.categories', [], 301);
        }
        return $this->render('admin/blog/crud_categories/create.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true',
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'éditer une catégorie
     *
     * @param BlogCategory $category
     * @param Request $request
     * @return Response
     */
    public function edit(BlogCategory $category, Request $request): Response {
        $form = $this->createForm(BlogCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée');
            return $this->redirectToRoute('admin.blog.manage.categories', [], 301);
        }
        return $this->render('admin/blog/crud_categories/edit.html.twig', [
            'current_menu' => 'blog-categories-manage',
            'is_dashboard' => 'true',
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une catégorie
     *
     * @param BlogCategory $category
     * @param Request $request
     * @return Response
     */
    public function delete(BlogCategory $category, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token'))) {
            $this->manager->remove($category);
            $this->manager->flush();
            $this->addFlash('success', 'La suppression est un succès !');
        }
        return $this->redirectToRoute('admin.blog.manage.categories', [], 301);
    }
}