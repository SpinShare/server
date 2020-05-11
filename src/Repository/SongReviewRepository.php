<?php

namespace App\Repository;

use App\Entity\SongReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SongReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongReview[]    findAll()
 * @method SongReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SongReview::class);
    }

    // /**
    //  * @return SongReview[] Returns an array of SongReview objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SongReview
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
