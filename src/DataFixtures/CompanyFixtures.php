<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use libphonenumber\PhoneNumberUtil;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CompanyFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(protected SluggerInterface $slugger) {}

    public static function getGroups(): array
    {
        return ['company'];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Faker\Provider\fr_FR\Company($faker));
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $companyRawPhoneNumber = $faker->mobileNumber();
        $companyPhoneNumberObject = $phoneNumberUtil->parse($companyRawPhoneNumber, 'FR');

        $company = new Company();
        $company->setName('Ecoride')
            ->setSlug($this->slugger->slug($company->getName())->lower())
            ->setAdress($faker->streetAddress())
            ->setPostalCode($faker->postcode())
            ->setCity($faker->city)
            ->setPhone($companyPhoneNumberObject)
            ->setEmail($faker->companyEmail())
            ->setSiren($faker->siret())
            ->setManager($faker->name())
            ->setUrl($faker->url())
            ->setType(Company::TYPE_SAS);

        $manager->persist($company);

        $manager->flush();
    }
}

// Commande PHP dans le terminal : php bin/console doctrine:fixtures:load --group=company --append