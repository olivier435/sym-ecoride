<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\TripRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/mes-trajets/conducteur')]
#[IsGranted('ROLE_USER')]
final class TripDriverController extends AbstractController
{
    #[Route('/', name: 'app_trip_driver_list', methods: ['GET'])]
    public function __invoke(TripRepository $tripRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $trips = $tripRepository->findUpcomingByDriver($user);

        return $this->render('trip/history.html.twig', [
            'trips' => $trips,
            'role' => 'driver',
        ]);
    }

    #[Route('/{id}', name: 'app_trip_driver_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip): Response
    {
        $user = $this->getUser();
        // Sécurité : seul le driver peut accéder à SON trajet
        if ($trip->getDriver() !== $user) {
            throw $this->createAccessDeniedException("Ce trajet ne vous appartient pas.");
        }

        return $this->render('trip/detail_history.html.twig', [
            'trip' => $trip,
            'role' => 'driver'
        ]);
    }
}