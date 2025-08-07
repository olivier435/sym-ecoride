<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trip')]
final class TripPublicController extends AbstractController
{
    #[Route('/detail/{id}-{slug}', name: 'app_trip_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip, string $slug, SluggerInterface $slugger)
    {
        if (!$trip || !in_array($trip->getStatus(), Trip::STATUSES, true)) {
            throw $this->createNotFoundException('Ce covoiturage n\'existe pas.');
        }

        // Vérification du slug et redirection si mauvais slug
        $expectedSlug = $slugger->slug($trip->getSlugSource())->lower();

        if ($slug !== (string) $expectedSlug) {
            return $this->redirectToRoute('app_trip_detail', [
                'id' => $trip->getId(),
                'slug' => (string) $expectedSlug,
            ]);
        }

        $driver = $trip->getDriver();
        $placesLeft = $trip->getSeatsAvailable() - $trip->getPassengers()->count();

        // Récupère les préférences du conducteur
        $travelPreference = $driver->getTravelPreference();

        // Prépare la liste d'avis (à implémenter plus tard)
        // $reviews = $reviewRepository->findBy(['driver' => $driver], ['createdAt' => 'DESC']);

        return $this->render('trip_public/detail.html.twig', [
            'trip' => $trip,
            'travelPreference' => $travelPreference,
            'isFull' => $placesLeft <= 0,
            // 'reviews' => $reviews,
        ]);
    }
}