<?php

namespace App\Service;

use App\Entity\LoginAttempt;
use App\Entity\User;
use App\Repository\LoginAttemptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class LoginAttemptService {

    const ATTEMPTS = 3;

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var LoginAttemptRepository
     */
    private $repository;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        EntityManagerInterface $manager,
        LoginAttemptRepository $repository,
        EventDispatcherInterface $dispatcher) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    public function addAttempt(User $user): void {
        $attempt = (new LoginAttempt())->setUser($user);
        $this->manager->persist($attempt);
        $this->manager->flush();
    }

    public function limitReachedFor(User $user): bool {
        return $this->repository->countAttempt($user, 15) >= self::ATTEMPTS;
    }
}