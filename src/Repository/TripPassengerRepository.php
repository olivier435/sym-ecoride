<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\TripPassenger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripPassenger>
 */
class TripPassengerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripPassenger::class);
    }

    public function countValidated(): int
    {
        return (int) $this->createQueryBuilder('tp')
            ->select('COUNT(tp.id)')
            ->where('tp.validationStatus IN (:statuses)')
            ->setParameter('statuses', ['validated', 'reported'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCompletedByDay(): array
    {
        $result = $this->createQueryBuilder('tp')
            ->select("DATE(tp.validationAt) AS day, COUNT(tp.id) AS count")
            ->where('tp.validationStatus IN (:statuses)')
            ->andWhere('tp.validationAt IS NOT NULL')
            ->setParameter('statuses', ['validated', 'reported'])
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $date = \DateTime::createFromFormat('Y-m-d', $row['day']);
            $formatted = $date ? $date->format('d-m-Y') : $row['day'];
            $data[$formatted] = (int) $row['count'];
        }
        return $data;
    }

    public function sumCreditsByDay(): array
    {
        $result = $this->createQueryBuilder('tp')
            ->select("DATE(tp.validationAt) AS day, COUNT(tp.id) * 2 AS credits")
            ->where('tp.validationStatus IN (:statuses)')
            ->andWhere('tp.validationAt IS NOT NULL')
            ->setParameter('statuses', ['validated', 'reported'])
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $date = \DateTime::createFromFormat('Y-m-d', $row['day']);
            $formatted = $date ? $date->format('d-m-Y') : $row['day'];
            $data[$formatted] = (int) $row['credits'];
        }
        return $data;
    }

    public function findPendingToAutoValidate(\DateTimeImmutable $now): array
    {
        $qb = $this->createQueryBuilder('tp')
            ->join('tp.trip', 't')
            ->leftJoin('tp.complaint', 'c')
            ->where('tp.validationStatus = :pending')
            ->andWhere('c.id IS NULL')
            ->andWhere('t.status = :completed')
            ->andWhere('t.arrivalDate IS NOT NULL')
            ->andWhere('t.arrivalTime IS NOT NULL')
            ->andWhere("DATE_ADD(CONCAT(t.arrivalDate, ' ', t.arrivalTime), 1, 'HOUR') <= :now")
            ->setParameter('pending', 'pending')
            ->setParameter('completed', Trip::STATUS_COMPLETED)
            ->setParameter('now', $now->format('Y-m-d H:i:s'));

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return TripPassenger[] Returns an array of TripPassenger objects
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

    //    public function findOneBySomeField($value): ?TripPassenger
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}