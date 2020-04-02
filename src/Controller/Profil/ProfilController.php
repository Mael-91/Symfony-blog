<?php

namespace App\Controller\Profil;

use App\Entity\User;
use App\Event\CloudinaryDeleteEvent;
use App\Event\CloudinaryUploadEvent;
use App\Exceptions\UserNotConnectedException;
use App\Form\ProfilDetailsType;
use App\Form\ProfilEmailSecurityType;
use App\Form\ProfilSecurityPasswordType;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProfilController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var TokenGeneratorService
     */
    private $tokenService;
    /**
     * @var PasswordService
     */
    private $passwordService;

    public function __construct(
        EntityManagerInterface $manager,
        UserRepository $userRepository,
        BlogRepository $blogRepository,
        UserPasswordEncoderInterface $encoder,
        EventDispatcherInterface $dispatcher,
        TokenGeneratorService $tokenService,
        PasswordService $passwordService) {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->blogRepository = $blogRepository;
        $this->encoder = $encoder;
        $this->dispatcher = $dispatcher;
        $this->tokenService = $tokenService;
        $this->passwordService = $passwordService;
    }

    public function profil(Request $request): Response {
        $user = $this->getId($request);
        $articles = $this->blogRepository->findBy(['author' => $user]);
        return $this->render('profil/profil.html.twig', [
            'current_menu' => 'profil',
            'is_dashboard' => 'false',
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    public function security(Request $request, User $user): Response {
        $profil = $this->getId($request);
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') ||
            !$this->isGranted('IS_AUTHENTICATED_REMEMBERED') ||
            !$this->isGranted('ROLE_USER')) {
            // TODO Renvoyer vers la page de login avec une message d'erreur sur celle-ci pour informer l'utilisateur
            throw new UserNotConnectedException();
        }
        $passwordForm = $this->createForm(ProfilSecurityPasswordType::class, $user);
        $emailForm = $this->createForm(ProfilEmailSecurityType::class, $user);
        $passwordForm->handleRequest($request);
        $emailForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->passwordService->resetPassword($user);
            // TODO Mettre en place le processus en JSON pour le dynamisme
        }

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $this->manager->flush();
            // TODO Mettre en place le processus en JSON pour le dynamisme
        }

        return $this->render('profil/security.html.twig', [
            'current_menu' => 'profil-security',
            'is_dashboard' => 'false',
            'user' => $profil,
            'securityForm' => $passwordForm->createView(),
            'emailForm' => $emailForm->createView()
        ]);
    }

    public function details(Request $request, User $user): Response {
        $user = $this->getId($request);
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY') ||
            !$this->isGranted('IS_AUTHENTICATED_REMEMBERED') ||
            !$this->isGranted('ROLE_USER')) {
            // TODO Renvoyer vers la page de login avec une message d'erreur sur celle-ci pour informer l'utilisateur
            throw new UserNotConnectedException();
        }

        $form = $this->createForm(ProfilDetailsType::class, $user);
        $form->handleRequest($request);
        $avatarName = $user->getAvatarFilename();
        $bannerName = $user->getBannerFilename();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->uploadImage($user, $avatarName, $bannerName, $user->getAvatarFile(), $user->getBannerFile());
            $this->addFlash('success', 'Your informations were successfully updated');
            // TODO faire le processus en JSON / JS
        }

        return $this->render('profil/details.html.twig', [
            'current_menu' => 'profil-details',
            'is_dashboard' => 'false',
            'user' => $user,
            'detailsForm' => $form->createView(),
        ]);
    }

    private function getId(Request $request): User {
        $profil = $request->get('id');
        return $this->userRepository->findOneBy(['id' => $profil]);
    }

    private function uploadImage(User $user, string $avatarName, string $bannerName, ?File $newAvatar, ?File $newBanner): void {
        if ($user->getAvatarFilename() != $avatarName || is_null($avatarName)) {
            $this->dispatcher->dispatch(new CloudinaryUploadEvent($newAvatar, 'user/avatar', null, 250, 250), CloudinaryUploadEvent::NAME);
            $this->dispatcher->dispatch(new CloudinaryDeleteEvent('user/avatar', null, $avatarName), CloudinaryDeleteEvent::NAME);
        }
        if ($user->getBannerFilename() != $bannerName || is_null($bannerName)) {
            $this->dispatcher->dispatch(new CloudinaryUploadEvent($newBanner, 'user/banner', null, 1920, 250), CloudinaryUploadEvent::NAME);
            $this->dispatcher->dispatch(new CloudinaryDeleteEvent('user/banner', null, $bannerName), CloudinaryDeleteEvent::NAME);
        }
    }
}