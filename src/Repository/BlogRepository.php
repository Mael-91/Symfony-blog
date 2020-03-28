<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    /**
     * @return Query
     */
    public function findAllActiveQuery(): Query {
        return $this->findActiveQuery()
            ->getQuery();
    }

    /**
     * @return Blog[] array
     */
    public function findLatest(): array {
        return $this->findActiveQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    public function findlastArticle(): array {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findPostsInCategory($category) {
        $manager = $this->getEntityManager()->getConnection();

        $sql = 'SELECT p.*, p.active = true, c.name as category_name, c.slug as category_slug
                FROM blog as p LEFT JOIN blog_category as c ON  c.id = p.category_id
                WHERE p.category_id = :category ORDER BY p.created_at DESC';
        $stmt = $manager->prepare($sql);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll();
    }

    public function findWithCategory($postId) {
        $manager = $this->getEntityManager()->getConnection();

        $sql = 'SELECT p.category_id, c.name as category_name, c.slug as category_slug FROM blog
                as p LEFT JOIN blog_category as c ON c.id = p.category_id WHERE p.id = :postID';
        $stmt = $manager->prepare($sql);
        $stmt->execute(['postID' => $postId]);
        return $stmt->fetchAll();
    }

    public function countPost() {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function findActiveQuery(): QueryBuilder {
        return $this->createQueryBuilder('p')
            ->where('p.active = true');
    }
}
