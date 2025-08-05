<?php

namespace App\Controller\Trip;

use App\Entity\User;
use App\Repository\TripRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/mes-trajets/conducteur', name: 'app_trip_driver_list')]
#[IsGranted('ROLE_USER')]
final class TripDriverController extends AbstractController
{
    public function __invoke(TripRepository $tripRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $trips = $tripRepository->findUpcomingByDriver($user);

        return $this->render('trip/driver_list.html.twig', [
            'trips' => $trips,
        ]);
    }
}