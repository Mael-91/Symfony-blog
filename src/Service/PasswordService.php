<?php

namespace App\Service;

use App\Entity\PasswordToken;
use App\Entity\User;
use App\Event\RequestChangePasswordEvent;
use App\Exceptions\OAuthPasswordException;
use App\Exceptions\TokenExpiredException;
use App\Repository\PasswordTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use MongoDB\Driver\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PasswordService {

    const EXPIRE_IN = 15;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var PasswordTokenRepository
     */
    private $tokenRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var TokenGeneratorService
     */
    private $generatorService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $manager,
        PasswordTokenRepository $tokenRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        TokenGeneratorService $generatorService,
        EventDispatcherInterface $dispatcher) {
        $this->manager = $manager;
        $this->tokenRepository = $tokenRepository;
        $this->encoder = $encoder;
        $this->generatorService = $generatorService;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
    }

    /**
     * Permet de faire la demande de changement de mot de passe
     * @param User $user
     * @throws \Exception
     */
    public function resetPassword(User $user): void {
        $token = $this->tokenRepository->findOneBy(['user' => $user]);
        $isOAuth = $this->userRepository->findOneBy(['id' => $user]);
        if ($isOAuth->getOauth() === true) {
            throw new OAuthPasswordException();
        }
        if ($token !== null && !$this->isExpired($token)) {
            throw new TokenExpiredException();
        }
        if ($token !== null && $this->isExpired($token)) {
            $this->manager->remove($token);
            $this->manager->flush();
        }
        $token = new PasswordToken();
        $token->setUser($user)
            ->setToken($this->generatorService->generateToken());
        $this->manager->persist($token);
        $this->manager->flush();
        $this->dispatcher->dispatch(new RequestChangePasswordEvent($token));
    }

    /**
     * Permet de mettre à jour le MDP
     * @param string $password
     * @param PasswordToken $token
     */
    public function updatePassword(string $password, PasswordToken $token): void {
        $user = $token->getUser();
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $this->manager->remove($token);
        $this->manager->flush();
    }

    /**
     * Permet de vérifier si le token est expiré
     * @param PasswordToken $token
     * @return bool
     * @throws \Exception
     */
    public function isExpired(PasswordToken $token): bool {
        $expiration = new \DateTime('-' . self::EXPIRE_IN . 'minutes');
        return $token->getCreatedAt() < $expiration;
    }
}