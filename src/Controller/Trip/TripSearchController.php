<?php

namespace App\Controller\Trip;

use App\Form\TripSearchType;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
final class TripSearchController extends AbstractController
{
    #[Route('', name: 'app_trip_search')]
    public function search(Request $request, TripRepository $tripRepository): Response
    {
        $departureCity = $request->query->get('departureCity');
        $arrivalCity = $request->query->get('arrivalCity');
        $dateString = $request->query->get('date');

        $prefillData = [];
        $dateObj = null;

        if ($departureCity) {
            $prefillData['departureCity'] = $departureCity;
        }

        if ($arrivalCity) {
            $prefillData['arrivalCity'] = $arrivalCity;
        }

        if ($dateString) {
            $dateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $dateString);
            if ($dateObj instanceof \DateTimeImmutable) {
                // 🔧 Important : normaliser à minuit
                $dateObj = $dateObj->setTime(0, 0);
                $prefillData['date'] = $dateObj;
            }
        }

        // Formulaire avec données préremplies
        $form = $this->createForm(TripSearchType::class, $prefillData);
        $form->handleRequest($request);

        // 📩 Si formulaire soumis, redirection avec les paramètres GET
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('app_trip_search', [
                'departureCity' => $data['departureCity'],
                'arrivalCity'   => $data['arrivalCity'],
                'date'          => $data['date']->format('Y-m-d'),
            ]);
        }

        $trips = [];
        $nextAvailableDate = null;
        $isSubmitted = $departureCity && $arrivalCity && $dateObj;

        if ($isSubmitted) {
            $trips = $tripRepository->findMatchingTrips($departureCity, $arrivalCity, $dateObj);

            if (empty($trips)) {
                $nextAvailableDate = $tripRepository->findNextAvailableDate($departureCity, $arrivalCity, $dateObj);
            }
        }

        return $this->render('trip_search/search.html.twig', [
            'form' => $form->createView(),
            'trips' => $trips,
            'nextAvailableDate' => $nextAvailableDate,
            'isSubmitted' => $isSubmitted,
        ]);
    }
}