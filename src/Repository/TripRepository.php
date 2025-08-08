<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Car;
use App\Entity\City;
use App\Entity\Trip;
use App\Entity\User;
use App\Enum\PetPreference;
use App\Enum\SmokingPreference;
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

    public function findFilteredTrips(SearchData $search): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('car', 'driver', 'avatar')
            ->join('t.car', 'car')
            ->join('t.driver', 'driver')
            ->leftJoin('driver.avatar', 'avatar')
            ->leftJoin('driver.travelPreference', 'tp')
            ->leftJoin('t.passengers', 'p')
            ->andWhere('t.departureCity = :departureCity')
            ->andWhere('t.arrivalCity = :arrivalCity')
            ->andWhere('t.departureDate = :date')
            ->setParameter('departureCity', $search->departureCity)
            ->setParameter('arrivalCity', $search->arrivalCity)
            ->setParameter('date', $search->date)
            ->groupBy('t.id');

        // Filtre sur l'heure si aujourd'hui
        $today = (new \DateTimeImmutable('now'))->format('Y-m-d');
        $searchDate = $search->date instanceof \DateTimeInterface ? $search->date->format('Y-m-d') : null;
        if ($searchDate === $today) {
            $now = (new \DateTimeImmutable())->format('H:i:s');
            $qb->andWhere('t.departureTime > :now')
                ->setParameter('now', $now);
        }

        // Véritable nombre de places restantes = seatsAvailable - COUNT(p)
        // $qb->having('t.seatsAvailable > COUNT(p.id)');

        if ($search->priceMax !== null) {
            $qb->andWhere('t.pricePerPerson <= :priceMax')
                ->setParameter('priceMax', $search->priceMax);
        }
        if ($search->eco) {
            $qb->andWhere('car.energy = :eco')
                ->setParameter('eco', Car::ENERGY_ELECTRIC);
        }
        if ($search->smoking) {
            $qb->andWhere('tp.smoking = :smoking')
                ->setParameter('smoking', SmokingPreference::ALLOWED->value); // ou adapte selon ton enum
        }
        if ($search->pets) {
            $qb->andWhere('tp.pets IN (:petsAllowed)')
                ->setParameter('petsAllowed', [
                    PetPreference::LOVES_PETS->value,
                    PetPreference::SOME_PETS->value,
                ]);
        }

        // Tri
        if ($search->sort === 'price') {
            $qb->orderBy('t.pricePerPerson', 'ASC');
        } elseif ($search->sort === 'duration') {
            $qb->addSelect(
                '(TIMESTAMPDIFF(MINUTE, CONCAT(t.departureDate, \' \', t.departureTime), CONCAT(t.arrivalDate, \' \', t.arrivalTime))) AS HIDDEN tripDuration'
            )->orderBy('tripDuration', 'ASC');
        } else {
            $qb->orderBy('t.departureTime', 'ASC');
        }

        return $qb->getQuery()->getResult();
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