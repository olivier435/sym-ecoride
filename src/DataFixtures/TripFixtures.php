<?php

namespace App\DataFixtures;

use App\Entity\Trip;
use App\Entity\User;
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

        // Cache des villes déjà créées (pour éviter les doublons et limiter les requêtes)
        $cityCache = [];

        foreach ($users as $user) {
            // On ne crée des trajets que si l'utilisateur a au moins un véhicule
            if (count($user->getCars()) === 0) {
                continue;
            }

            // Crée entre 1 et 3 trajets par utilisateur
            $tripCount = rand(1, 3);

            for ($i = 0; $i < $tripCount; $i++) {
                $trip = new Trip();

                // Dates cohérentes
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

                // Génère les adresses
                $departureAddress = $faker->streetAddress . ', ' . $faker->postcode . ' ' . mb_strtoupper($faker->city);
                $arrivalAddress = $faker->streetAddress . ', ' . $faker->postcode . ' ' . mb_strtoupper($faker->city);

                // Récupération/création des entités City
                $departureCity = $this->cityManager->getOrCreateCity($departureAddress);
                $arrivalCity   = $this->cityManager->getOrCreateCity($arrivalAddress);

                $trip->setDepartureAddress($departureAddress)
                    ->setArrivalAddress($arrivalAddress)
                    ->setDepartureCity($departureCity)
                    ->setArrivalCity($arrivalCity)
                    ->setStatus($faker->randomElement(Trip::STATUSES))
                    ->setSeatsAvailable($faker->numberBetween(1, 4))
                    // ->setPricePerPerson($faker->numberBetween(500, 4000));
                    ->setPricePerPerson($faker->numberBetween(2, 20));

                // Associations cohérentes
                $trip->setDriver($user);
                $car = $faker->randomElement($user->getCars()->toArray());
                $trip->setCar($car);

                // Passagers (autres users uniquement)
                $passengerCandidates = array_filter($users, fn(User $u) => $u !== $user);
                $nbPassengers = rand(0, min(count($passengerCandidates), $trip->getSeatsAvailable()));

                if ($nbPassengers > 0) {
                    $passengers = $faker->randomElements($passengerCandidates, $nbPassengers);
                    foreach ($passengers as $passenger) {
                        $trip->addPassenger($passenger);
                    }
                }

                $manager->persist($trip);
            }
        }

        $manager->flush();
    }
}