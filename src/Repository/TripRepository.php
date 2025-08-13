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
        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);
        $today = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.driver = :driver')
            // Exclure orphelins expirés :
            ->andWhere('
            NOT (
                SIZE(t.tripPassengers) = 0
                AND (
                    t.departureDate < :today
                    OR (t.departureDate = :today AND t.departureTime < :time)
                )
            )
        ')
            ->setParameter('driver', $driver)
            ->setParameter('today', $today)
            ->setParameter('time', $time)
            ->orderBy('t.departureDate', 'ASC')
            ->addOrderBy('t.departureTime', 'ASC');

        return $qb->getQuery()->getResult();
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

    public function findFilteredTrips(SearchData $search, TestimonialRepository $testimonialRepository): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('car', 'driver', 'avatar')
            ->join('t.car', 'car')
            ->join('t.driver', 'driver')
            ->leftJoin('driver.avatar', 'avatar')
            ->leftJoin('driver.travelPreference', 'tp')
            ->leftJoin('t.tripPassengers', 'tpax')
            ->andWhere('t.departureCity = :departureCity')
            ->andWhere('t.arrivalCity = :arrivalCity')
            ->andWhere('t.departureDate = :date')
            ->setParameter('departureCity', $search->departureCity)
            ->setParameter('arrivalCity', $search->arrivalCity)
            ->setParameter('date', $search->date)
            ->groupBy('t.id, driver.id');

        $tz = new \DateTimeZone('Europe/Paris');
        $today = (new \DateTimeImmutable('now', $tz))->format('Y-m-d');
        $searchDate = $search->date instanceof \DateTimeInterface ? $search->date->format('Y-m-d') : null;

        if ($searchDate === $today) {
            $now = (new \DateTimeImmutable('now', $tz))->format('H:i:s');
            $qb->andWhere('t.departureTime > :now')
                ->setParameter('now', $now);
        }

        $qb->having('t.seatsAvailable > COUNT(tpax.id)');

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
                ->setParameter('smoking', SmokingPreference::ALLOWED->value);
        }
        if ($search->pets) {
            $qb->andWhere('tp.pets IN (:petsAllowed)')
                ->setParameter('petsAllowed', [
                    PetPreference::LOVES_PETS->value,
                    PetPreference::SOME_PETS->value,
                ]);
        }

        if ($search->sort === 'price') {
            $qb->orderBy('t.pricePerPerson', 'ASC');
        } elseif ($search->sort === 'duration') {
            $qb->addSelect(
                '(TIMESTAMPDIFF(MINUTE, CONCAT(t.departureDate, \' \', t.departureTime), CONCAT(t.arrivalDate, \' \', t.arrivalTime))) AS HIDDEN tripDuration'
            )->orderBy('tripDuration', 'ASC');
        } else {
            $qb->orderBy('t.departureTime', 'ASC');
        }

        $trips = $qb->getQuery()->getResult();

        // Calcul de la note moyenne des conducteurs
        $drivers = array_map(fn($trip) => $trip->getDriver(), $trips);
        $driverIds = array_map(fn($driver) => $driver->getId(), $drivers);
        $avgRatings = $testimonialRepository->getAvgRatingsForDriversByIds($driverIds);

        foreach ($drivers as $driver) {
            $driver->avgRating = $avgRatings[$driver->getId()] ?? 0;
        }

        // Filtrage Super Driver
        if ($search->superDriver) {
            $trips = array_filter($trips, fn($trip) => ($trip->getDriver()->avgRating ?? 0) >= 4.7);
        }

        return $trips;
    }

    public function findTripsToAutoStart(\DateTimeImmutable $now): array
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // On ne veut que les trajets "à venir", aujourd'hui, et l'heure est <= maintenant (avec une marge si besoin)
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->andWhere('t.departureDate = :date')
            ->andWhere('t.departureTime <= :time')
            // Optionnel : ne pas basculer trop longtemps après l'heure de départ (ex: max +1h)
            //->andWhere('t.departureTime >= :minTime')
            ->setParameter('status', Trip::STATUS_UPCOMING)
            ->setParameter('date', $date)
            ->setParameter('time', $time)
            //->setParameter('minTime', ...)
            ->getQuery()
            ->getResult();
    }

    public function findTripsToAutoComplete(\DateTimeImmutable $now): array
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // On veut les trajets "en cours" pour aujourd'hui,
        // dont l'heure d'arrivée est <= maintenant (ou dans une certaine fenêtre)
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->andWhere('t.arrivalDate = :date')
            ->andWhere('t.arrivalTime <= :time')
            // Optionnel : on ne met à jour que jusqu'à X heures après l'heure d'arrivée prévue
            //->andWhere('t.arrivalTime >= :minTime')
            ->setParameter('status', Trip::STATUS_ONGOING)
            ->setParameter('date', $date)
            ->setParameter('time', $time)
            //->setParameter('minTime', ...)
            ->getQuery()
            ->getResult();
    }
}