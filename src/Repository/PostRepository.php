<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[]
     */
    public function findByMonth(\DateTimeImmutable $month, int $limit = 10): array
    {
        if (0 > $limit) {
            throw new \InvalidArgumentException('Limit must be a positive integer');
        }

        // Puisque le QueryBuilder est créé à partir du dépôt centré sur l'entité "Post",
        // il est préconfiguré pour faire un "SELECT", et le "FROM" est paramétré sur l'entité en question
        return $this->createQueryBuilder('post')
            ->andWhere('post.createdAt >= :from')
            ->andWhere('post.createdAt < :to')
            ->setMaxResults($limit)
            ->getQuery() // On a fini de construire la requête
            ->setParameter('from', $month->modify('first day of this month midnight'))
            ->setParameter('to', $month->modify('first day of next month midnight'))
            ->getResult(); // Par défaut, le type d'hydratation du résultat est des instances de l'entité Post
    }

    /**
     * @return Post[]
     */
    public function findByMonthDql(\DateTimeImmutable $month, int $limit = 10): array
    {
        if (0 > $limit) {
            throw new \InvalidArgumentException('Limit must be a positive integer');
        }

        return $this->getEntityManager()->createQuery(
            'SELECT post FROM '.Post::class.' post '
            .' WHERE post.createdAt >= :from'
            .' AND post.createdAt < :to'
        )
            ->setMaxResults($limit)
            ->setParameter('from', $month->modify('first day of this month midnight'))
            ->setParameter('to', $month->modify('first day of next month midnight'))
            ->getResult();
    }

    public function findHavingTag(array $tagIds)
    {
        $qb = $this->createQueryBuilder('post');
        $filters = [];
        for ($i = 0; $i < count($tagIds); ++$i) {
            $filters[] = "?$i MEMBER OF post.tags";
        }
        $qb->andWhere(
            $qb->expr()->orX(
                ...$filters
            )
        );

        for ($i = 0; $i < count($tagIds); ++$i) {
            $qb->setParameter($i, (int) $tagIds[$i]);
        }
        return $qb
            ->getQuery()
            ->getResult();
    }
}
