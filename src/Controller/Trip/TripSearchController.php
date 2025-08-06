<?php

namespace App\Controller\Trip;

use App\Entity\Car;
use App\Form\TripSearchType;
use App\Repository\TripRepository;
use App\Entity\City;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
final class TripSearchController extends AbstractController
{
    #[Route('', name: 'app_trip_search')]
    public function search(Request $request, TripRepository $tripRepository, CityRepository $cityRepository): Response
    {
        $form = $this->createForm(TripSearchType::class);
        $form->handleRequest($request);

        $trips = [];
        $nextAvailableDate = null;
        $isSubmitted = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var City|null $departureCity */
            $departureCity = $data['departureCity'] ?? null;
            /** @var City|null $arrivalCity */
            $arrivalCity = $data['arrivalCity'] ?? null;
            /** @var \DateTimeImmutable|null $date */
            $date = $data['date'] ?? null;

            $isSubmitted = $departureCity && $arrivalCity && $date;

            if ($isSubmitted) {
                $trips = $tripRepository->findMatchingTrips($departureCity, $arrivalCity, $date);
                if (empty($trips)) {
                    $nextAvailableDate = $tripRepository->findNextAvailableDate($departureCity, $arrivalCity, $date);
                }
            }
        }

        return $this->render('trip_search/search.html.twig', [
            'form' => $form->createView(),
            'trips' => $trips,
            'nextAvailableDate' => $nextAvailableDate,
            'isSubmitted' => $isSubmitted,
        ]);
    }

    #[Route('/ajax', name: 'app_trip_search_ajax', methods: ['GET'])]
    public function searchAjax(Request $request, TripRepository $tripRepository, CityRepository $cityRepository): JsonResponse
    {
        try {
            $departureCityId = $request->query->get('departureCity');
            $arrivalCityId = $request->query->get('arrivalCity');
            $dateString = $request->query->get('date');

            if (!$departureCityId || !$arrivalCityId || !$dateString) {
                return $this->json([
                    'trips' => [],
                    'nextAvailableDate' => null,
                    'isSubmitted' => false,
                ]);
            }

            // On va chercher les entités City à partir des ID passés par le front
            $departureCity = $cityRepository->find($departureCityId);
            $arrivalCity = $cityRepository->find($arrivalCityId);

            if (!$departureCity || !$arrivalCity) {
                return $this->json([
                    'trips' => [],
                    'nextAvailableDate' => null,
                    'isSubmitted' => false,
                ]);
            }

            $dateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $dateString)?->setTime(0, 0);

            $trips = $tripRepository->findMatchingTrips($departureCity, $arrivalCity, $dateObj);

            $tripsArray = array_map(function ($trip) {
                $driver = $trip->getDriver();
                $avatarEntity = $driver?->getAvatar();
                $avatarName = $avatarEntity?->getImageName();
                $avatarPath = '/images/avatars/' . $avatarName;
                return [
                    'id' => $trip->getId(),
                    'driver' => [
                        'pseudo' => $driver?->getPseudo() ?? '',
                        'avatar' => $avatarPath,
                    ],
                    'departureDate' => $trip->getDepartureDate()?->format('d/m/Y'),
                    'departureTime' => $trip->getDepartureTime()?->format('H:i'),
                    'arrivalDate' => $trip->getArrivalDate()?->format('d/m/Y'),
                    'arrivalTime' => $trip->getArrivalTime()?->format('H:i'),
                    'departureAddress' => $trip->getDepartureAddress() ?? '',
                    'arrivalAddress' => $trip->getArrivalAddress() ?? '',
                    'seatsAvailable' => $trip->getSeatsAvailable(),
                    'pricePerPerson' => number_format($trip->getPricePerPerson() / 100, 2, ',', ' '),
                    'isEco' => $trip->getCar()?->getEnergy() === Car::ENERGY_ELECTRIC,
                ];
            }, $trips);

            $nextAvailableDate = null;
            if (empty($trips)) {
                $next = $tripRepository->findNextAvailableDate($departureCity, $arrivalCity, $dateObj);
                $nextAvailableDate = $next?->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

        return $this->json([
            'trips' => $tripsArray,
            'nextAvailableDate' => $nextAvailableDate,
            'isSubmitted' => true,
        ]);
    }
}