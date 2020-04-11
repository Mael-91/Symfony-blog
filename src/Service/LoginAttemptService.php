<?php

namespace App\Service;

use App\Entity\LoginAttempt;
use App\Entity\User;
use App\Repository\LoginAttemptRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    public function __construct(
        EntityManagerInterface $manager,
        LoginAttemptRepository $repository) {
        $this->manager = $manager;
        $this->repository = $repository;
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