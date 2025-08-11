<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Service\CityManager;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Route('/mes-trajets')]
#[IsGranted('ROLE_USER')]
final class TripCancelController extends AbstractController
{
    #[Route('/delete/{id}', name: 'app_trip_delete', methods: ['POST'])]
    public function cancel(Request $request, Trip $trip, EntityManagerInterface $em, CityManager $cityManager, SendMailService $mailer): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$trip->isCancellable()) {
            return new JsonResponse(['error' => 'Ce trajet ne peut pas être supprimé.'], Response::HTTP_BAD_REQUEST);
        }

        // On protège par le token
        if (!$this->isCsrfTokenValid('cancel' . $trip->getId(), $request->request->get('_token'))) {
            return new JsonResponse(['error' => 'Token invalide'], Response::HTTP_FORBIDDEN);
        }

        $isDriver = $trip->getDriver() === $user;
        $isPassenger = false;
        $tripPassengerOfUser = null;
        foreach ($trip->getTripPassengers() as $tp) {
            if ($tp->getUser() === $user) {
                $isPassenger = true;
                $tripPassengerOfUser = $tp;
                break;
            }
        }

        if (!$isDriver && !$isPassenger) {
            return new JsonResponse(['error' => "Vous n'êtes pas concerné par ce trajet."], Response::HTTP_FORBIDDEN);
        }

        if ($isDriver) {
            // Le conducteur annule le trajet : tous les passagers sont remboursés et notifiés
            foreach ($trip->getTripPassengers() as $tripPassenger) {
                $passenger = $tripPassenger->getUser();
                $passenger->setCredit($passenger->getCredit() + $trip->getPricePerPerson());
                $mailer->sendMail(
                    $user->getFullName(),
                    $passenger->getEmail(),
                    'Trajet annulé par le conducteur',
                    'trip_cancelled_by_driver',
                    [
                        'passenger' => $passenger,
                        'trip' => $trip,
                        'driver' => $user,
                    ]
                );
                $trip->removeTripPassenger($tripPassenger);
                $em->persist($passenger);
            }
            $trip->setStatus(Trip::STATUS_CANCELLED);
            $em->persist($trip);
            $this->addFlash('success', 'Votre trajet a bien été annulé. Tous les passagers ont été remboursés.');
        } else {
            // Un passager annule sa réservation
            $mailer->sendMail(
                $user->getFullName(),
                $trip->getDriver()->getEmail(),
                'Un passager a annulé sa réservation',
                'trip_cancelled_by_passenger',
                [
                    'driver' => $trip->getDriver(),
                    'trip' => $trip,
                    'passenger' => $user,
                ]
            );
            $user->setCredit($user->getCredit() + $trip->getPricePerPerson());
            if ($tripPassengerOfUser) {
                $trip->removeTripPassenger($tripPassengerOfUser);
            }
            $em->persist($user);
            $em->persist($trip);
            $this->addFlash('success', 'Votre réservation a bien été annulée. Vous avez été remboursé.');
        }
        $em->flush();
        $cityManager->purgeOrphanCities();

        return new JsonResponse([
            'success' => true,
            'redirect' => $this->generateUrl($isDriver ? 'app_trip_driver_list' : 'app_trip_passenger_list')
        ]);
    }
}