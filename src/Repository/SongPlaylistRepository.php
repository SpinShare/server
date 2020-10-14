<?php

namespace App\Repository;

use App\Entity\SongPlaylist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SongPlaylist|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongPlaylist|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongPlaylist[]    findAll()
 * @method SongPlaylist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongPlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SongPlaylist::class);
    }

    // /**
    //  * @return SongPlaylist[] Returns an array of SongPlaylist objects
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
    public function findOneBySomeField($value): ?SongPlaylist
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
