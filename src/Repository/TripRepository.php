<?php

namespace App\Repository;

use App\Entity\City;
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

    public function findMatchingTrips(City $departureCity, City $arrivalCity, \DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('car', 'driver', 'avatar')
            ->join('t.car', 'car')
            ->join('t.driver', 'driver')
            ->leftJoin('driver.avatar', 'avatar')
            ->andWhere('t.seatsAvailable > 0')
            ->andWhere('t.departureCity = :departureCity')
            ->andWhere('t.arrivalCity = :arrivalCity')
            ->andWhere('t.departureDate = :date')
            ->setParameter('departureCity', $departureCity)
            ->setParameter('arrivalCity', $arrivalCity)
            ->setParameter('date', $date)
            ->orderBy('t.departureTime', 'ASC');

        return $qb->getQuery()
            ->getResult();
    }

    public function findNextAvailableDate(City $departureCity, City $arrivalCity, \DateTimeInterface $after): ?\DateTimeInterface
    {
        $result = $this->createQueryBuilder('t')
            ->select('t.departureDate')
            ->andWhere('t.seatsAvailable > 0')
            ->andWhere('t.departureCity = :departureCity')
            ->andWhere('t.arrivalCity = :arrivalCity')
            ->andWhere('t.departureDate > :after')
            ->setParameter('departureCity', $departureCity)
            ->setParameter('arrivalCity', $arrivalCity)
            ->setParameter('after', $after)
            ->orderBy('t.departureDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

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