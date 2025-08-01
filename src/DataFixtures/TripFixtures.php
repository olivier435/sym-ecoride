<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TripFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['trip'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();
        $cars = $manager->getRepository(Car::class)->findAll();

        if (empty($users) || empty($cars)) {
            throw new \RuntimeException('Il faut d\'abord charger les utilisateurs et les voitures avant de charger les covoiturages.');
        }

        for ($i = 0; $i < 15; $i++) {
            $trip = new Trip();

            // Création des dates cohérentes
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

            $trip->setDepartureAddress($faker->address())
                ->setArrivalAddress($faker->address())
                ->setStatus($faker->randomElement(Trip::STATUSES))
                ->setSeatsAvailable($faker->numberBetween(1, 4))
                ->setPricePerPerson($faker->numberBetween(500, 4000));

            // Création des relations
            $driver = $faker->randomElement($users);
            $trip->setDriver($driver);

            $car = $faker->randomElement($cars);
            $trip->setCar($car);

            // Ajout de quelques passagers (sauf le driver)
            $passengerCandidates = array_filter($users, fn(User $u) => $u !== $driver);
            $nbPassengers = rand(0, min(count($passengerCandidates), $trip->getSeatsAvailable()));
            if ($nbPassengers > 0) {
                $passengers = $faker->randomElements($passengerCandidates, $nbPassengers);
                foreach ($passengers as $passenger) {
                    $trip->addPassenger($passenger);
                }
            }

            $manager->persist($trip);
        }

        $manager->flush();
    }
}