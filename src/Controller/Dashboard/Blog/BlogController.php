<?php

namespace App\Controller\Dashboard\Blog;

use App\Entity\Blog;
use App\Event\CloudinaryDeleteEvent;
use App\Event\CloudinaryUploadEvent;
use App\Event\UserActivityEvent;
use App\Form\BlogType;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogRepository;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlogController extends AbstractController {

    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var BlogCategoryRepository
     */
    private $categoryRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var BlogCommentRepository
     */
    private $commentRepository;
    /**
     * @var CloudinaryService
     */
    private $cloudinaryService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        BlogRepository $blogRepository,
        BlogCategoryRepository $categoryRepository,
        BlogCommentRepository $commentRepository,
        EntityManagerInterface $manager,
        CloudinaryService $cloudinaryService,
        EventDispatcherInterface $dispatcher) {
        $this->blogRepository = $blogRepository;
        $this->categoryRepository = $categoryRepository;
        $this->manager = $manager;
        $this->commentRepository = $commentRepository;
        $this->cloudinaryService = $cloudinaryService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Response
     */
    public function index(): Response {
        $numPost = $this->blogRepository->countPost();
        $numCategory = $this->categoryRepository->countCategory();
        $numComment = $this->commentRepository->countComment();
        return $this->render('admin/blog/dashboard_blog.html.twig', [
            'current_menu' => 'dashboard-blog',
            'is_dashboard' => 'true',
            'numbersPost' => $numPost,
            'numbersCategory' => $numCategory,
            'numbersComment' => $numComment
        ]);
    }

    /**
     * Liste les postes
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function managePost(PaginatorInterface $paginator, Request $request): Response {
        $posts = $paginator->paginate($this->blogRepository->findAll(),
            $request->query->getInt('page', '1'), 20);
        return $this->render('admin/blog/posts.html.twig', [
            'current_menu' => 'blog-posts-manage',
            'is_dashboard' => 'true',
            'posts' => $posts
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setAuthor($this->getUser());
            $this->manager->persist($blog);
            $this->manager->flush();
            $this->addFlash('success-blog', 'La publication est un succès !');
            $this->dispatcher->dispatch(new CloudinaryUploadEvent($blog->getPictureFile(), 'blog', null, 360, 230), CloudinaryUploadEvent::NAME);
            $this->dispatcher->dispatch(new CloudinaryUploadEvent($blog->getBannerFile(), 'blog', null, null, null), CloudinaryUploadEvent::NAME);
            $this->dispatcher->dispatch(new UserActivityEvent($this->getUser(), $blog, null, null));
            return $this->redirectToRoute('admin.blog.manage.post');
        }
        return $this->render('admin/blog/crud_posts/create.html.twig', [
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
        $pictureName = $blog->getPictureFilename();
        $bannerName = $blog->getBannerFilename();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $newPicture = $blog->getPictureFile();
            $newBanner = $blog->getBannerFile();
            if ($blog->getPictureFilename() != $pictureName) {
                $this->dispatcher->dispatch(new CloudinaryUploadEvent($newPicture, 'blog', null, 360, 230), CloudinaryUploadEvent::NAME);
                $this->dispatcher->dispatch(new CloudinaryDeleteEvent('blog', null, $pictureName), CloudinaryDeleteEvent::NAME);
            } elseif ($blog->getBannerFilename() != $bannerName) {
                $this->dispatcher->dispatch(new CloudinaryUploadEvent($newBanner, 'blog', null, null, null), CloudinaryUploadEvent::NAME);
                $this->dispatcher->dispatch(new CloudinaryDeleteEvent('blog', null, $bannerName), CloudinaryDeleteEvent::NAME);
            }
            $this->addFlash('success-blog', 'La modification est un succès !');
            return $this->redirectToRoute('admin.blog.manage.post');
        }
        return $this->render('admin/blog/crud_posts/edit.html.twig', [
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
    public function delete(Blog $blog, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->get('_token'))) {
            $this->dispatcher->dispatch(new CloudinaryDeleteEvent('blog', null, $blog->getPictureFilename()), CloudinaryDeleteEvent::NAME);
            $this->dispatcher->dispatch(new CloudinaryDeleteEvent('blog', null, $blog->getBannerFilename()), CloudinaryDeleteEvent::NAME);
            $this->manager->remove($blog);
            $this->manager->flush();
            $this->addFlash('success-blog', 'La suppression est un succès !');
        }
        return $this->redirectToRoute('admin.blog.manage.post');
    }
}