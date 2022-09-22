<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }


    public function add(Post $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function remove(Post $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllPublishedOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->orderBy('p.publishedAt','DESC')
            ->getQuery()
            ->getResult()
            ;

//        $criteria = Criteria::create()
//            ->andWhere(Criteria::expr()->neq('publishedAt', null))
//            ->orderBy(['publishedAt', Criteria::DESC])
//        ;
//
//        return $this->matching($criteria);
    }

    public function findAllPublishedOrderedQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->orderBy('p.publishedAt','DESC')
            ->getQuery()
            ;
    }


    public function findOneByPublishDateAndSlug(string $date, string $slug): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('DATE(p.publishedAt) = :date')
            ->andWhere('p.slug = :slug')
            ->setParameters([
                'date' => $date,
                'slug' => $slug
            ])
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
