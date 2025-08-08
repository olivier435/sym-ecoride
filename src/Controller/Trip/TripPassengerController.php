<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[Route('/mes-trajets/passager')]
#[IsGranted('ROLE_USER')]
final class TripPassengerController extends AbstractController
{
    #[Route('/', name: 'app_trip_passenger_list', methods: ['GET'])]
    public function list(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $trips = $user->getTripsAsPassenger();

        return $this->render('trip/history.html.twig', [
            'trips' => $trips,
            'role' => 'passenger'
        ]);
    }

    #[Route('/{id}', name: 'app_trip_passenger_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip): Response
    {
        $user = $this->getUser();
        // Sécurité : le user doit être passager
        if (!$trip->getPassengers()->contains($user)) {
            throw $this->createAccessDeniedException("Vous ne participez pas à ce trajet.");
        }

        return $this->render('trip/detail_history.html.twig', [
            'trip' => $trip,
            'role' => 'passenger'
        ]);
    }
}