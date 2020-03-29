<?php


namespace App\Controller\Dashboard\Users;


use App\Entity\User;
use App\Form\UserCreateType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder) {

        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Permet d'afficher tout les utilisateurs
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $users = $paginator->paginate($this->userRepository->findAll(),
            $request->query->getInt('page', '1'), 20);
        return $this->render('admin/users/users.html.twig', [
            'current_menu' => 'dashboard-users',
            'is_dashboard' => 'true',
            'users' => $users
        ]);
    }

    /**
     * Permet de créer un utilisateur
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response {
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles($user->getRoles());
            $user->setCreatedAt(new \DateTime('now'));
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success-user', 'L\'utilisateur a bien été modifié');
            return $this->redirectToRoute('admin.users', [], 301);
        }
        return $this->render('admin/users/crud/create.html.twig', [
            'current_menu' => 'dashboard-users',
            'is_dashboard' => 'true',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'éditer un utilisateur
     *
     * @param User $user
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function edit(User $user, Request $request): Response {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEditedAt(new \DateTime('now'));
            $this->manager->flush();
            $this->addFlash('success-user', 'L\'utilisateur a bien été modifié');
            return $this->redirectToRoute('admin.users', [], 301);
        }
        return $this->render('admin/users/crud/edit.html.twig', [
            'current_menu' => 'dashboard-users',
            'is_dashboard' => 'true',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function delete(User $user, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))) {
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('success-user', 'La suppression de l\'utilisateur est un succès');
        }
        return $this->redirectToRoute('admin.users', [], 301);
    }
}