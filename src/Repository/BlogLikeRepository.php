<?php

namespace App\Repository;

use App\Entity\BlogLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BlogLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogLike[]    findAll()
 * @method BlogLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogLike::class);
    }

    // /**
    //  * @return BlogLike[] Returns an array of BlogLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlogLike
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
