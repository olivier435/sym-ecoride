<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[Route('/trip/detail')]
#[IsGranted('ROLE_USER')]
final class TripReservationController extends AbstractController
{
    #[Route('/{id}-{slug}/reservation', name: 'app_trip_reservation_recap', methods: ['GET'])]
    public function recap(Request $request, Trip $trip, string $slug): Response
    {
        // 1. Sécurité côté back
        /** @var User $user */
        $user = $this->getUser();
        $driver = $trip->getDriver();
        $isFull = $trip->isFull();
        $placesLeft = $trip->getSeatsLeft();
        $travelPreference = $driver->getTravelPreference();

        // Si pas de places restantes
        if ($trip->isFull()) {
            return $this->json(['error' => 'Trajet complet'], 400);
        }
        // Si le passager est déjà inscrit
        if ($trip->getPassengers()->contains($user)) {
            return $this->json(['error' => 'Vous participez déjà à ce trajet'], 400);
        }
        // Si l'utilisateur est le conducteur
        if ($trip->getDriver() === $user) {
            return $this->json(['error' => 'Vous êtes le conducteur du trajet'], 400);
        }
        // Si crédits insuffisants
        if ($user->getCredit() < $trip->getPricePerPerson()) {
            return $this->json(['error' => 'Vous n\'avez pas assez de crédits'], 400);
        }

        // 2. Affichage selon AJAX ou HTTP classique
        if ($request->isXmlHttpRequest()) {
            // Partial à injecter dynamiquement via Stimulus
            return $this->render('trip_reservation/_recap_partial.html.twig', [
                'trip' => $trip,
                'slug' => $slug,
                'travelPreference' => $travelPreference,
                'isFull' => $isFull,
                'placesLeft' => $placesLeft,
            ]);
        }

        // Affichage classique
        return $this->render('trip_public/detail.html.twig', [
            'trip' => $trip,
            'slug' => $slug,
            'travelPreference' => $travelPreference,
            'isFull' => $isFull,
            'placesLeft' => $placesLeft,
        ]);
    }

    #[Route('/{id}-{slug}/reservation/prix', name: 'app_trip_reservation_price', methods: ['GET'])]
    public function priceDetail(Trip $trip, string $slug): Response
    {
        $priceTotal = $trip->getPricePerPerson();
        $fee = 2;
        $priceDriver = $priceTotal - $fee;

        return $this->render('trip_reservation/price_detail.html.twig', [
            'trip' => $trip,
            'slug' => $slug,
            'priceTotal' => $priceTotal,
            'fee' => $fee,
            'priceDriver' => $priceDriver,
        ]);
    }
}