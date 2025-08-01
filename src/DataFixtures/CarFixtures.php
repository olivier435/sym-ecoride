<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Car;
use App\Entity\Model;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CarFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['car'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();
        $brands = $manager->getRepository(Brand::class)->findAll();
        $models = $manager->getRepository(Model::class)->findAll();

        if (empty($users) || empty($brands) || empty($models)) {
            throw new \RuntimeException('Les utilisateurs, marques et modèles doivent être chargés avant les voitures.');
        }

        for ($i = 0; $i < 15; $i++) {
            $car = new Car();

            $car->setEnergy($faker->randomElement(Car::ENERGIES));

            // Génération d'une plaque d'immatriculation réaliste FR
            $registration = sprintf(
                '%s-%03d-%s',
                $faker->randomLetter() . $faker->randomLetter(),
                $faker->numberBetween(100, 999),
                $faker->randomLetter() . $faker->randomLetter()
            );
            $car->setRegistration(strtoupper($registration));

            // Génération de la couleur
            $color = ucfirst($faker->safeColorName());
            $car->setColor($color);

            // Date de 1ère immatriculation
            $firstRegistration = $faker->dateTimeBetween('-10 years', 'now');
            $car->setFirstregistrationAt(\DateTimeImmutable::createFromMutable($firstRegistration));

            // Relation User
            $car->setUser($faker->randomElement($users));

            // Relation Brand et Model cohérente
            $brand = $faker->randomElement($brands);
            $car->setBrand($brand);

            // Filtrer les modèles qui correspondent à la marque
            $modelsForBrand = array_filter($models, fn(Model $model) => $model->getBrand() === $brand);
            if (!empty($modelsForBrand)) {
                $car->setModel($faker->randomElement($modelsForBrand));
            } else {
                // Cas exceptionnel où aucun modèle n'existe pour la marque
                continue; // on saute cette voiture
            }

            $manager->persist($car);
        }

        $manager->flush();
    }
}