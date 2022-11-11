<?php

namespace App\Repository;

use App\Entity\UserReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserReport[]    findAll()
 * @method UserReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReport::class);
    }
}
