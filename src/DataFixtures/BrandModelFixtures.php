<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Model;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class BrandModelFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['brandModel'];
    }

    public function load(ObjectManager $manager): void
    {
        $brandsAndModels = [
            'Peugeot' => ['208', '308', '3008', '5008'],
            'Renault' => ['Clio', 'Captur', 'Mégane', 'Scénic'],
            'Citroën' => ['C3', 'C4', 'C5 Aircross'],
            'BMW' => ['Série 1', 'Série 3', 'X1', 'i3'],
            'Mercedes-Benz' => ['Classe A', 'Classe C', 'GLA', 'EQC'],
            'Volkswagen' => ['Golf', 'Polo', 'Tiguan', 'ID.3'],
            'Tesla' => ['Model 3', 'Model Y', 'Model S', 'Model X'],
            'Toyota' => ['Yaris', 'Corolla', 'RAV4', 'Prius'],
            'Audi' => ['A3', 'A4', 'Q3', 'e-tron'],
            'Ford' => ['Fiesta', 'Focus', 'Puma', 'Kuga'],
            'Opel' => ['Corsa', 'Astra', 'Mokka'],
            'Hyundai' => ['i20', 'i30', 'Kona', 'Ioniq'],
            'Kia' => ['Picanto', 'Ceed', 'Sportage', 'EV6'],
            'Dacia' => ['Sandero', 'Duster', 'Spring'],
            'Nissan' => ['Micra', 'Juke', 'Qashqai', 'Leaf'],
        ];

        foreach ($brandsAndModels as $brandName => $modelNames) {
            $brand = new Brand();
            $brand->setName($brandName);
            $manager->persist($brand);

            foreach ($modelNames as $modelName) {
                $model = new Model();
                $model->setName($modelName)
                    ->setBrand($brand);
                $manager->persist($model);
            }
        }

        $manager->flush();
    }
}