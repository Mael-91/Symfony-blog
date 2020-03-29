<?php

namespace App\Repository;

use App\Entity\LoginAttempt;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LoginAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginAttempt[]    findAll()
 * @method LoginAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    public function countAttempt(User $user, int $minute): int {
        return $this->createQueryBuilder('la')
            ->select('COUNT(la.id)')
            ->where('la.user = :user')
            ->andWhere('la.created_at > :date')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTime("-{$minute} minutes"))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
