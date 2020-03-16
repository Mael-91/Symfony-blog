<?php

namespace App\Repository;

use App\Entity\BlogComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method BlogComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogComment[]    findAll()
 * @method BlogComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogComment::class);
    }

    public function findAllActive() {
        return $this->findActiveQuery()
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCommentForPost($postId) {
        return $this->findActiveQuery()
            ->where('c.post = :post_id')
            ->addOrderBy('c.created_at')
            ->setParameter('post_id', $postId)
            ->getQuery()
            ->getResult();
    }

    public function countCommentInPost($postId) {
        return $this->createQueryBuilder('c')
            ->where('c.post = :postId')
            ->setParameter('postId', $postId)
            ->select('COUNT(c.post)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function findActiveQuery(): QueryBuilder {
        return $this->createQueryBuilder('c')
            ->where('c.visible = true');
    }

    // /**
    //  * @return BlogComment[] Returns an array of BlogComment objects
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
    public function findOneBySomeField($value): ?BlogComment
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
