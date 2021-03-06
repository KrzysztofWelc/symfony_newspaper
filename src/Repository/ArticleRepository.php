<?php
/**
 * Article repo.
 */

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Article repo.
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    const PAGINATOR_ITEMS_PER_PAGE = 10;


    /**
     * constructor for ArticleRepository.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('article.createdAt', 'DESC');
    }

    /**
     * Save record.
     *
     * @param \App\Entity\Article $article Article entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Article $article): void
    {
        $this->_em->persist($article);
        $this->_em->flush($article);
    }

    /**
     * Delete record.
     *
     * @param \App\Entity\Article $article Article entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Article $article): void
    {
        $this->_em->remove($article);
        $this->_em->flush($article);
    }

    /**
     * Returns articles of passed user.
     *
     * @param User $usr user entity
     *
     * @return QueryBuilder
     */
    public function getUsersArticles(User $usr): QueryBuilder
    {
        $qb = $this->getOrCreateQueryBuilder();

        $qb
            ->select()
            ->andWhere('article.author = :author')
            ->orderBy('article.createdAt', 'DESC')
            ->setParameter('author', $usr)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    /**
     * Get published articles.
     *
     * @return QueryBuilder
     */
    public function getPublishedArticles(): QueryBuilder
    {
        $qb = $this->getOrCreateQueryBuilder();

        $qb
            ->select()
            ->andWhere('article.isPublished = true')
            ->orderBy('article.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    /**
     * Get or create new query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('article');
    }
}
