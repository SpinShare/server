<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Song|null find($id, $lockMode = null, $lockVersion = null)
 * @method Song|null findOneBy(array $criteria, array $orderBy = null)
 * @method Song[]    findAll()
 * @method Song[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    public function getNew(int $page) {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->where('e.publicationStatus = 0')
            ->orderBy('e.uploadDate', 'DESC')
            ->setFirstResult(12 * $page)
            ->setMaxResults(12);
        return $qb->getQuery()->getResult();
    }

    public function getUpdated(int $page) {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->where('e.publicationStatus = 0')
            ->andWhere('e.updateDate != e.uploadDate')
            ->orderBy('e.updateDate', 'DESC')
            ->addOrderBy('e.uploadDate', 'DESC')
            ->setFirstResult(12 * $page)
            ->setMaxResults(12);
        return $qb->getQuery()->getResult();
    }

    public function getHotThisWeek(int $page) {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->where('e.publicationStatus = 0')
            ->andWhere('e.uploadDate <= :begin')
            ->andWhere('e.uploadDate >= :end')
            ->orderBy('e.downloads', 'DESC')
            ->addOrderBy('e.views', 'DESC')
            ->setFirstResult(12 * $page)
            ->setMaxResults(12)
            ->setParameter('begin', new \DateTime('NOW'))
            ->setParameter('end', new \DateTime('-7 days'));
        return $qb->getQuery()->getResult();
    }

    public function getHotThisMonth(int $page) {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->where('e.publicationStatus = 0')
            ->andWhere('e.uploadDate <= :begin')
            ->andWhere('e.uploadDate >= :end')
            ->orderBy('e.downloads', 'DESC')
            ->addOrderBy('e.views', 'DESC')
            ->setFirstResult(12 * $page)
            ->setMaxResults(12)
            ->setParameter('begin', new \DateTime('NOW'))
            ->setParameter('end', new \DateTime('-1 month'));
        return $qb->getQuery()->getResult();
    }

    public function getPopular(int $page) {
        return [];
    }
}
