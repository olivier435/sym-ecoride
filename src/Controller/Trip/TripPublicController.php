<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trip')]
final class TripPublicController extends AbstractController
{
    #[Route('/detail/{id}-{slug}', name: 'app_trip_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip, string $slug, SluggerInterface $slugger, Request $request)
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
        $isFull = $trip->isFull();
        $placesLeft = $trip->getSeatsLeft();

        // Récupère les préférences du conducteur
        $travelPreference = $driver->getTravelPreference();

        // Prépare la liste d'avis (à implémenter plus tard)
        // $reviews = $reviewRepository->findBy(['driver' => $driver], ['createdAt' => 'DESC']);

        if ($request->isXmlHttpRequest()) {
            // AJAX : On retourne UNIQUEMENT le partial !
            return $this->render('trip_public/_detail_partial.html.twig', [
                'trip' => $trip,
                'travelPreference' => $travelPreference,
                'slug' => $slug,
                'isFull' => $isFull,
                'placesLeft' => $placesLeft,
            ]);
        }

        return $this->render('trip_public/detail.html.twig', [
            'trip' => $trip,
            'travelPreference' => $travelPreference,
            'isFull' => $isFull,
            'placesLeft' => $placesLeft,
            'slug' => $slug,
            // 'reviews' => $reviews,
        ]);
    }
}