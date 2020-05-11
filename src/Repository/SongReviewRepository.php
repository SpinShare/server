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

    public function getAveragebyID(int $songId) {
        $qb = $this->createQueryBuilder("e");
        $qb
            ->where('e.song = :songId')
            ->setParameter('songId', $songId);
        $reviews = $qb->getQuery()->getResult();

        $reviewsTotal = 0;
        $reviewsRecommend = 0;

        foreach($reviews as $review) {
            if($review->getRecommended()) $reviewsRecommend++;
            $reviewsTotal++;
        }

        if($reviewsTotal > 1) {
            return round(($reviewsRecommend / $reviewsTotal) * 100, 1);
        } else {
            return false;
        }
    }
}
