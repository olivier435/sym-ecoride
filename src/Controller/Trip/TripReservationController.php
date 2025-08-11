<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\TripPassenger;
use App\Entity\User;
use App\Service\TripReservationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Route('/trip/detail')]
#[IsGranted('ROLE_USER')]
final class TripReservationController extends AbstractController
{
    use TripContextTrait;

    #[Route('/{id}-{slug}/reservation', name: 'app_trip_reservation_recap', methods: ['GET'])]
    public function recap(Request $request, Trip $trip, string $slug, TripReservationValidator $validator): Response
    {
        // 1. Sécurité côté back
        /** @var User $user */
        $user = $this->getUser();

        // Utilisation du service de validation
        [$error, $code] = $validator->validate($trip, $user);
        if ($error) {
            return $this->json(['error' => $error], $code);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('trip_reservation/_recap_partial.html.twig', $this->getTripContext($trip, $slug));
        }

        return $this->render('trip_public/detail.html.twig', $this->getTripContext($trip, $slug));
    }

    #[Route('/{id}-{slug}/reservation/prix', name: 'app_trip_reservation_price', methods: ['GET'])]
    public function priceDetail(Trip $trip, string $slug): Response
    {
        $priceTotal = $trip->getPricePerPerson();
        $fee = 2;
        $priceDriver = $priceTotal - $fee;

        return $this->render('trip_reservation/price_detail.html.twig', [
            'trip' => $trip,
            'slug' => $slug,
            'priceTotal' => $priceTotal,
            'fee' => $fee,
            'priceDriver' => $priceDriver,
        ]);
    }

    #[Route('/{id}-{slug}/reservation/confirm', name: 'app_trip_reservation_confirm', methods: ['GET'])]
    public function confirm(Trip $trip, string $slug, TripReservationValidator $validator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        // Utilisation du service de validation
        [$error, $code] = $validator->validate($trip, $user);
        if ($error) {
            return $this->json(['error' => $error], $code);
        }

        return $this->render('trip_reservation/_second_confirmation.html.twig', [
            'trip' => $trip,
            'slug' => $slug,
        ]);
    }

    #[Route('/{id}-{slug}/reservation/book', name: 'app_trip_reservation_book', methods: ['POST'])]
    public function book(Trip $trip, EntityManagerInterface $em, TripReservationValidator $validator): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        // Utilisation du service de validation
        [$error, $code] = $validator->validate($trip, $user);
        if ($error) {
            return $this->json(['error' => $error], $code);
        }

        // Vérifie que le passager n'est pas déjà inscrit
        foreach ($trip->getTripPassengers() as $tp) {
            if ($tp->getUser() === $user) {
                return $this->json(['error' => 'Vous êtes déjà inscrit sur ce trajet.'], 400);
            }
        }

        $user->setCredit($user->getCredit() - $trip->getPricePerPerson());

        $tripPassenger = new TripPassenger();
        $tripPassenger->setTrip($trip)
                    ->setUser($user)
                    ->setValidationStatus('pending');
        $em->persist($tripPassenger);

        $em->persist($user);
        $em->persist($trip);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été enregistrée ! Vous serez débité uniquement si le trajet n\'est pas annulé.');

        return $this->json(['success' => true]);
    }
}