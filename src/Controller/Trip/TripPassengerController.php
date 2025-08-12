<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\TestimonialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        $trips = [];
        foreach ($user->getTripPassengers() as $tp) {
            $trips[] = $tp->getTrip();
        }

        return $this->render('trip/history.html.twig', [
            'trips' => $trips,
            'role' => 'passenger'
        ]);
    }

    #[Route('/{id}', name: 'app_trip_passenger_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip, TestimonialRepository $testimonialRepository): Response
    {
        $user = $this->getUser();
        $tripPassenger = null;
        foreach ($trip->getTripPassengers() as $tp) {
            if ($tp->getUser() === $user) {
                $tripPassenger = $tp;
                break;
            }
        }

        $driver = $trip->getDriver();

        $avgRating = $testimonialRepository->getAvgRatingsForDriver($driver->getId());
        $totalCount = $testimonialRepository->getTotalCountForDriver($driver->getId());

        if (!$tripPassenger) {
            throw $this->createAccessDeniedException("Vous ne participez pas à ce trajet.");
        }

        $isCancellable = $trip->isCancellable();
        $isCompleted = $trip->isCompleted();
        // $isValidated = $trip->isValidated();
        // $isReported = $trip->isReported();
        $validationStatus = $tripPassenger->getValidationStatus();


        return $this->render('trip/detail_history.html.twig', [
            'trip' => $trip,
            'role' => 'passenger',
            'isCancellable' => $isCancellable,
            'isCompleted' => $isCompleted,
            'avgRating' => $avgRating,
            'totalCount' => $totalCount,
            'validationStatus' => $validationStatus,
        ]);
    }

    #[Route('/{id}/validate', name: 'app_trip_passenger_validate', methods: ['POST'])]
    public function validate(Request $request, Trip $trip, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tripPassenger = null;
        foreach ($trip->getTripPassengers() as $tp) {
            if ($tp->getUser() === $user) {
                $tripPassenger = $tp;
                break;
            }
        }
        if (!$tripPassenger) {
            throw $this->createAccessDeniedException();
        }
        if (!$trip->isCompleted()) {
            $this->addFlash('danger', 'Ce trajet ne peut pas être validé.');
            return $this->redirectToRoute('app_trip_passenger_detail', ['id' => $trip->getId()]);
        }
        if (!$this->isCsrfTokenValid('validate' . $trip->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide');
            return $this->redirectToRoute('app_trip_passenger_detail', ['id' => $trip->getId()]);
        }

        if ($tripPassenger->getValidationStatus() === 'pending') {
            $tripPassenger->setValidationStatus('validated');
            $tripPassenger->setValidationAt(new \DateTimeImmutable());
            $driver = $trip->getDriver();
            $driver->setCredit($driver->getCredit() + $trip->getPricePerPerson() - 2);
            $em->persist($tripPassenger);
            $em->persist($driver);
            $em->flush();
            $this->addFlash('success', 'Merci pour votre validation ! Pour finaliser, merci de laisser un avis');
        } else {
            $this->addFlash('info', 'Vous avez déjà validé ce trajet.');
        }
        return $this->redirectToRoute('app_trip_passenger_testimonial_new', ['id' => $trip->getId()]);
        // return $this->redirectToRoute('app_trip_passenger_list');
    }
}