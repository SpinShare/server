<?php

namespace App\Repository;

use App\Entity\SongSpinPlay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SongSpinPlay|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongSpinPlay|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongSpinPlay[]    findAll()
 * @method SongSpinPlay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongSpinPlayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SongSpinPlay::class);
    }

    // /**
    //  * @return SongSpinPlay[] Returns an array of SongSpinPlay objects
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
    public function findOneBySomeField($value): ?SongSpinPlay
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
