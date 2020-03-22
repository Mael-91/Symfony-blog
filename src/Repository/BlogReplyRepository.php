<?php

namespace App\Repository;

use App\Entity\BlogReply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method BlogReply|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogReply|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogReply[]    findAll()
 * @method BlogReply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogReply::class);
    }

    public function findChildrenComment($commentId) {
        $manager = $this->getEntityManager()->getConnection();

        $sql = 'SELECT r.*, r.visible = true, c.id as comment_id 
                FROM blog_reply as r LEFT JOIN blog_comment as c ON c.id = r.comment_id 
                WHERE r.comment_id = :commentId';
        $stmt = $manager->prepare($sql);
        $stmt->execute(['commentId' => $commentId]);
        return $stmt->fetchAll();
    }
}
