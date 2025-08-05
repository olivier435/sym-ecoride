<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findUpcomingByDriver(User $driver): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.driver = :driver')
            ->setParameter('driver', $driver)
            ->orderBy('t.departureDate', 'ASC')
            ->addOrderBy('t.departureTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMatchingTrips(string $departureCity, string $arrivalCity, \DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('car', 'driver', 'avatar')
            ->join('t.car', 'car')
            ->join('t.driver', 'driver')
            ->leftJoin('driver.avatar', 'avatar')
            ->andWhere('t.seatsAvailable > 0')
            ->andWhere('LOWER(t.departureCity) = :departureCity')
            ->andWhere('LOWER(t.arrivalCity) = :arrivalCity')
            ->andWhere('t.departureDate = :date')
            ->setParameter('departureCity', mb_strtolower($departureCity))
            ->setParameter('arrivalCity', mb_strtolower($arrivalCity))
            ->setParameter('date', $date instanceof \DateTimeImmutable ? $date : \DateTimeImmutable::createFromMutable($date))
            ->orderBy('t.departureTime', 'ASC');

        return $qb->getQuery()
            ->getResult();
    }

    public function findNextAvailableDate(string $departureCity, string $arrivalCity, \DateTimeInterface $after): ?\DateTimeInterface
    {
        $result = $this->createQueryBuilder('t')
            ->select('t.departureDate')
            ->andWhere('t.seatsAvailable > 0')
            ->andWhere('LOWER(t.departureCity) = :departureCity')
            ->andWhere('LOWER(t.arrivalCity) = :arrivalCity')
            ->andWhere('t.departureDate > :after')
            ->orderBy('t.departureDate', 'ASC')
            ->setParameter('departureCity', mb_strtolower($departureCity))
            ->setParameter('arrivalCity', mb_strtolower($arrivalCity))
            ->setParameter('after', $after)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // ✅ On accède au champ du tableau retourné
        return $result['departureDate'] ?? null;
    }

    //    /**
    //     * @return Trip[] Returns an array of Trip objects
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

    //    public function findOneBySomeField($value): ?Trip
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}