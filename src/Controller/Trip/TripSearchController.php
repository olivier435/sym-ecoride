<?php

namespace App\Controller\Trip;

use App\Data\SearchData;
use App\Entity\Car;
use App\Form\TripSearchType;
use App\Repository\TripRepository;
use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/search')]
final class TripSearchController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger) {}

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

            /** @var \App\Entity\City|null $departureCity */
            $departureCity = $data['departureCity'] ?? null;
            /** @var \App\Entity\City|null $arrivalCity */
            $arrivalCity = $data['arrivalCity'] ?? null;
            /** @var \DateTimeImmutable|null $date */
            $date = $data['date'] ?? null;

            // Si jamais l'autocomplete retourne l'ID et pas l'entité (possible selon config UX)
            if (is_numeric($departureCity)) {
                $departureCity = $cityRepository->find($departureCity);
            }
            if (is_numeric($arrivalCity)) {
                $arrivalCity = $cityRepository->find($arrivalCity);
            }

            $isSubmitted = $departureCity instanceof City && $arrivalCity instanceof City && $date;

            if ($isSubmitted) {
                $trips = $tripRepository->findFilteredTrips($departureCity, $arrivalCity, $date);
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
    public function searchAjax(Request $request, TripRepository $tripRepository, CityRepository $cityRepository,
    TestimonialRepository $testimonialRepository): JsonResponse
    {
        try {
            $search = new SearchData();

            $search->departureCity = $cityRepository->find($request->query->get('departureCity'));
            $search->arrivalCity = $cityRepository->find($request->query->get('arrivalCity'));
            $dateString = $request->query->get('date');
            $search->date = $dateString ? \DateTimeImmutable::createFromFormat('Y-m-d', $dateString)?->setTime(0, 0) : null;

            // Gestion des filtres
            $search->sort = $request->query->get('sort');
            if ($search->sort === 'null' || $search->sort === '') {
                $search->sort = null;
            }
            $priceMax = $request->query->get('priceMax');
            // $search->priceMax = $priceMax !== '' && $priceMax !== null ? intval(floatval($priceMax) * 100) : null;
            $search->priceMax = $priceMax !== '' && $priceMax !== null ? intval($priceMax) : null;
            $search->eco = filter_var($request->query->get('eco'), FILTER_VALIDATE_BOOLEAN);
            $search->smoking = filter_var($request->query->get('smoking'), FILTER_VALIDATE_BOOLEAN);
            $search->pets = filter_var($request->query->get('pets'), FILTER_VALIDATE_BOOLEAN);
            $search->superDriver = filter_var($request->query->get('superDriver'), FILTER_VALIDATE_BOOLEAN);

            // Validation
            if (!$search->departureCity || !$search->arrivalCity || !$search->date) {
                return $this->json([
                    'trips' => [],
                    'nextAvailableDate' => null,
                    'isSubmitted' => false,
                ]);
            }

            $trips = $tripRepository->findFilteredTrips($search, $testimonialRepository);

            $tripsArray = array_map(function ($trip) {
                $driver = $trip->getDriver();
                $avatarEntity = $driver?->getAvatar();
                $avatarName = $avatarEntity?->getImageName();
                $avatarPath = $avatarName ? '/images/avatars/' . $avatarName : null;

                // Calcul de la durée du trajet (en minutes)
                $duration = null;
                if ($trip->getDepartureDate() && $trip->getDepartureTime() && $trip->getArrivalDate() && $trip->getArrivalTime()) {
                    $depart = new \DateTimeImmutable($trip->getDepartureDate()->format('Y-m-d') . ' ' . $trip->getDepartureTime()->format('H:i:s'));
                    $arrivee = new \DateTimeImmutable($trip->getArrivalDate()->format('Y-m-d') . ' ' . $trip->getArrivalTime()->format('H:i:s'));
                    $interval = $depart->diff($arrivee);
                    $duration = ($interval->h * 60) + $interval->i;
                }

                $isFull = $trip->isFull();

                // Ajout du slug PHP pour la route de détail
                $slug = $this->slugger->slug($trip->getSlugSource())->lower();

                return [
                    'id' => $trip->getId(),
                    'driver' => [
                        'pseudo' => $driver?->getPseudo() ?? '',
                        'avatar' => $avatarPath,
                        'avgRating' => $driver->avgRating ?? 0,
                    ],
                    'departureDate' => $trip->getDepartureDate()?->format('d/m/Y'),
                    'departureTime' => $trip->getDepartureTime()?->format('H:i'),
                    'arrivalDate' => $trip->getArrivalDate()?->format('d/m/Y'),
                    'arrivalTime' => $trip->getArrivalTime()?->format('H:i'),
                    'departureAddress' => $trip->getDepartureAddress() ?? '',
                    'arrivalAddress' => $trip->getArrivalAddress() ?? '',
                    'seatsAvailable' => $trip->getSeatsLeft(),
                    // 'pricePerPerson' => number_format($trip->getPricePerPerson() / 100, 2, ',', ' '),
                    'pricePerPerson' => $trip->getPricePerPerson(),
                    'isEco' => $trip->getCar()?->getEnergy() === Car::ENERGY_ELECTRIC,
                    'duration' => $duration,
                    'isFull' => $isFull,
                    'slug' => $slug,
                ];
            }, $trips);

            $nextAvailableDate = null;
            if (empty($trips)) {
                $next = $tripRepository->findNextAvailableDate($search->departureCity, $search->arrivalCity, $search->date);
                $nextAvailableDate = $next?->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

        return $this->json([
            'trips' => array_values($tripsArray),
            'nextAvailableDate' => $nextAvailableDate,
            'isSubmitted' => true,
        ]);
    }
}