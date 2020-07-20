<?php

namespace App\Repository;

use App\Entity\ConnectApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConnectApp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConnectApp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConnectApp[]    findAll()
 * @method ConnectApp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectAppRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConnectApp::class);
    }

    // /**
    //  * @return ConnectApp[] Returns an array of ConnectApp objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ConnectApp
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
