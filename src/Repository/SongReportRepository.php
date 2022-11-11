<?php

namespace App\Repository;

use App\Entity\SongReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SongReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method SongReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method SongReport[]    findAll()
 * @method SongReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SongReport::class);
    }
}
