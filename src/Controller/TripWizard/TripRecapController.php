<?php

namespace App\Controller\TripWizard;

use App\Repository\CarRepository;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/recap', name: 'app_trip_wizard_recap')]
#[IsGranted('ROLE_USER')]
final class TripRecapController extends AbstractController
{
    public function __invoke(TripCreationStorage $storage, CarRepository $carRepository): Response
    {
        $data = $storage->getData();

        // Si une voiture est sélectionnée, on la récupère pour l'afficher proprement
        $car = null;
        if (!empty($data['carId'])) {
            $car = $carRepository->find($data['carId']);
        }

        return $this->render('trip_wizard/recap.html.twig', [
            'data' => $data,
            'car' => $car,
        ]);
    }
}