<?php

namespace App\DataFixtures;

use App\Entity\Trip;
use App\Entity\User;
use App\Entity\TripPassenger;
use App\Service\CityManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TripFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private CityManager $cityManager) {}

    public static function getGroups(): array
    {
        return ['trip'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();

        if (empty($users)) {
            throw new \RuntimeException('Aucun utilisateur trouvé. Charge les UserFixtures d\'abord.');
        }

        foreach ($users as $user) {
            if (count($user->getCars()) === 0) {
                continue;
            }

            $tripCount = rand(1, 3);

            for ($i = 0; $i < $tripCount; $i++) {
                $trip = new Trip();

                $departureDate = $faker->dateTimeBetween('+1 day', '+30 days');
                $arrivalDate = clone $departureDate;
                $arrivalDate->modify('+' . rand(1, 2) . ' days');

                $trip->setDepartureDate(\DateTimeImmutable::createFromMutable($departureDate))
                    ->setArrivalDate(\DateTimeImmutable::createFromMutable($arrivalDate));

                $departureTime = $faker->dateTimeBetween('08:00:00', '20:00:00');
                $arrivalTime = clone $departureTime;
                $arrivalTime->modify('+' . rand(1, 4) . ' hours');

                $trip->setDepartureTime(\DateTimeImmutable::createFromMutable($departureTime))
                    ->setArrivalTime(\DateTimeImmutable::createFromMutable($arrivalTime));

                $departureAddress = $faker->streetAddress . ', ' . $faker->postcode . ' ' . mb_strtoupper($faker->city);
                $arrivalAddress = $faker->streetAddress . ', ' . $faker->postcode . ' ' . mb_strtoupper($faker->city);

                $departureCity = $this->cityManager->getOrCreateCity($departureAddress);
                $arrivalCity   = $this->cityManager->getOrCreateCity($arrivalAddress);

                $trip->setDepartureAddress($departureAddress)
                    ->setArrivalAddress($arrivalAddress)
                    ->setDepartureCity($departureCity)
                    ->setArrivalCity($arrivalCity)
                    ->setStatus($faker->randomElement(Trip::STATUSES))
                    ->setSeatsAvailable($faker->numberBetween(1, 4))
                    ->setPricePerPerson($faker->numberBetween(2, 20));

                $trip->setDriver($user);
                $car = $faker->randomElement($user->getCars()->toArray());
                $trip->setCar($car);

                $passengerCandidates = array_filter($users, fn(User $u) => $u !== $user);
                $nbPassengers = rand(0, min(count($passengerCandidates), $trip->getSeatsAvailable()));

                if ($nbPassengers > 0) {
                    $passengers = $faker->randomElements($passengerCandidates, $nbPassengers);
                    foreach ($passengers as $passenger) {
                        $tripPassenger = new TripPassenger();
                        $tripPassenger->setTrip($trip);
                        $tripPassenger->setUser($passenger);
                        $tripPassenger->setValidationStatus('pending');
                        $manager->persist($tripPassenger);
                        $trip->addTripPassenger($tripPassenger);
                    }
                }

                $manager->persist($trip);
            }
        }

        $manager->flush();
    }
}