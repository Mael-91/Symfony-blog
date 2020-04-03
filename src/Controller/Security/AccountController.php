<?php

namespace App\Controller\Security;

use App\Event\SecurityRegistrationEvent;
use App\Exceptions\TokenExpiredException;
use App\Repository\ConfirmationTokenRepository;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccountController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ConfirmationTokenRepository
     */
    private $tokenRepository;
    /**
     * @var TokenGeneratorService
     */
    private $service;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        EntityManagerInterface $manager,
        ConfirmationTokenRepository $tokenRepository,
        TokenGeneratorService $service,
        EventDispatcherInterface $dispatcher) {
        $this->manager = $manager;
        $this->tokenRepository = $tokenRepository;
        $this->service = $service;
        $this->dispatcher = $dispatcher;
    }

    public function confirm(string $token): RedirectResponse {
        $confirmationToken = $this->tokenRepository->findOneBy(['token' => $token]);
        if (is_null($confirmationToken)) {
            throw new TokenExpiredException();
        }
        $user = $confirmationToken->getUser();
        if ($user->getEnabled() === true) {
            $this->addFlash('error', 'Your account is already activated');
        }
        if (is_null($confirmationToken) || $token != $confirmationToken->getToken() || $this->service->isExpired($confirmationToken) || $user->getEnabled() != true) {
            $this->service->generateToken(10);
            $this->dispatcher->dispatch(new SecurityRegistrationEvent($user));
            throw new TokenExpiredException('The token has expired. An new email with the confirmation link for this account you was sent');
        }
        $user->setEnabled(true);
        $this->manager->remove($confirmationToken);
        $this->manager->flush();
        $this->addFlash('success', 'Your account is activated');
        return $this->redirectToRoute('home', [], 301);
    }

    // methode delete pour supprimer le compte
}