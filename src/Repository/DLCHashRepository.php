<?php

namespace App\Repository;

use App\Entity\DLCHash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DLCHash|null find($id, $lockMode = null, $lockVersion = null)
 * @method DLCHash|null findOneBy(array $criteria, array $orderBy = null)
 * @method DLCHash[]    findAll()
 * @method DLCHash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DLCHashRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DLCHash::class);
    }
}
