<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
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

        $isCancellable = $trip->isCancellable();
        $isCompleted = $trip->isCompleted();
        $isValidated = $trip->isValidated();
        $isReported = $trip->isReported();


        return $this->render('trip/detail_history.html.twig', [
            'trip' => $trip,
            'role' => 'passenger',
            'isCancellable' => $isCancellable,
            'isCompleted' => $isCompleted,
            'isValidated' => $isValidated,
            'isReported' => $isReported,
        ]);
    }

    #[Route('/{id}/validate', name: 'app_trip_passenger_validate', methods: ['POST'])]
    public function validate(Request $request, Trip $trip, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$trip->getPassengers()->contains($user)) {
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

        $trip->setStatus(Trip::STATUS_VALIDATED);
        // Mise à jour des crédits pour le driver
        $driver = $trip->getDriver();
        $driver->setCredit($driver->getCredit() + $trip->getPricePerPerson());

        $em->flush();

        $this->addFlash('success', 'Merci pour votre validation !');
        // Redirection vers un espace ou une page de remerciement/avis plus tard
        return $this->redirectToRoute('app_trip_passenger_list');
    }
}