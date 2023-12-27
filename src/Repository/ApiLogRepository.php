<?php

namespace App\Repository;

use App\Entity\ApiLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiLog>
 *
 * @method ApiLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiLog[]    findAll()
 * @method ApiLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiLog::class);
    }
}
