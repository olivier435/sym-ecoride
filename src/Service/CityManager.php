<?php

namespace App\Service;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CityManager
{
    public function __construct(
        private readonly CityRepository $cityRepository,
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Extrait le nom de ville d'une adresse complète, normalise (trim/majuscules).
     * Exemple : "5 Boulevard Vincent Gâche, 44200 NANTES" → "NANTES"
     */
    public function extractFromAddress(?string $address): ?string
    {
        if (!$address) {
            return null;
        }

        // On cherche une ville après un code postal à 5 chiffres
        if (preg_match('/\b\d{5}\s+([A-Za-zÀ-ÿ\-\' ]+)/u', $address, $matches)) {
            return mb_strtoupper(trim($matches[1]));
        }

        // Fallback : on prend le dernier morceau après la dernière virgule
        $parts = explode(',', $address);
        return mb_strtoupper(trim(end($parts)));
    }

    /**
     * Retourne l'entité City correspondant au nom extrait d'une adresse (créée si besoin).
     */
    public function getOrCreateCity(?string $address): ?City
    {
        $cityName = $this->extractFromAddress($address);
        if (!$cityName) return null;

        $normalized = mb_strtoupper(trim($cityName)); // On force l'UPPERCASE
        $city = $this->cityRepository->findOneBy(['name' => $normalized]);
        if (!$city) {
            $city = new City();
            $city->setName($normalized);
            $this->em->persist($city);
            // Pas de flush ici, c'est au controller de gérer
        }
        return $city;
    }

    /**
     * Supprime les villes orphelines (n'apparaissant dans aucun Trip).
     * Appel à faire APRES flush() d'un Trip.
     */
    public function purgeOrphanCities(): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('c')
            ->from(City::class, 'c')
            ->leftJoin('c.departureTrips', 'dtrips')
            ->leftJoin('c.arrivalTrips', 'atrips')
            ->where($qb->expr()->isNull('dtrips.id'))
            ->andWhere($qb->expr()->isNull('atrips.id'));

        $orphans = $qb->getQuery()->getResult();
        foreach ($orphans as $city) {
            $this->em->remove($city);
        }

        if (count($orphans) > 0) {
            $this->em->flush();
        }

        return count($orphans);
    }
}