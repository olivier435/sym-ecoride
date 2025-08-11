<?php

namespace App\Repository;

use App\Entity\Testimonial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Testimonial>
 */
class TestimonialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimonial::class);
    }

    public function findApprovedForDriver(int $driverId, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.trip', 'trip')
            ->where('t.isApproved = true')
            ->andWhere('trip.driver = :driver')
            ->setParameter('driver', $driverId)
            ->orderBy('t.id', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Calcule la moyenne des notes des avis pour un conducteur donné
     *
     * @param integer $driverId
     * @return float
     */
    public function getAvgRatingsForDriver(int $driverId): float
    {
        $avgRating = $this->createQueryBuilder('t')
            ->select('AVG(t.rating) as avgRating')
            ->join('t.trip', 'trip')
            ->where('t.isApproved = true')
            ->andWhere('trip.driver = :driver')
            ->setParameter('driver', $driverId)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $avgRating;
    }

    /**
     * Obtient le nombre total d'avis pour un driver donné
     *
     * @param integer $driverId
     * @return integer
     */
    public function getTotalCountForDriver(int $driverId): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->join('t.trip', 'trip')
            ->where('t.isApproved = true')
            ->andWhere('trip.driver = :driver')
            ->setParameter('driver', $driverId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Testimonial[] Returns an array of Testimonial objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Testimonial
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}