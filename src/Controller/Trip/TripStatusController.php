<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/trip')]
#[IsGranted('ROLE_USER')]
final class TripStatusController extends AbstractController
{
    #[Route('/driver/{id}/start', name: 'app_trip_start', methods: ['POST'])]
    public function start(Request $request, Trip $trip, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérification : seul le chauffeur peut démarrer le trajet et le trajet doit être "à venir"
        if ($trip->getDriver() !== $user || !$trip->canBeStarted()) {
            throw $this->createAccessDeniedException();
        }

        // CSRF
        if (!$this->isCsrfTokenValid('start' . $trip->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide');
            return $this->redirectToRoute('app_trip_driver_detail', ['id' => $trip->getId()]);
        }

        $trip->setStatus(Trip::STATUS_ONGOING);
        $em->flush();

        $this->addFlash('success', 'Le trajet a bien démarré.');
        return $this->redirectToRoute('app_trip_driver_detail', ['id' => $trip->getId()]);
    }

    #[Route('/driver/{id}/complete', name: 'app_trip_complete', methods: ['POST'])]
    public function complete(Request $request, Trip $trip, EntityManagerInterface $em, SendMailService $mailer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier autorisation et statut
        if ($trip->getDriver() !== $user || !$trip->canBeCompleted()) {
            throw $this->createAccessDeniedException();
        }

        // CSRF
        if (!$this->isCsrfTokenValid('complete' . $trip->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide');
            return $this->redirectToRoute('app_trip_driver_detail', ['id' => $trip->getId()]);
        }

        $trip->setStatus(Trip::STATUS_COMPLETED);

        foreach ($trip->getPassengers() as $passenger) {
            $mailer->sendMail(
                'Covoiturage',
                $passenger->getEmail(),
                'Merci de valider votre trajet',
                'passenger_validate_trip',
                [
                    'passenger' => $passenger,
                    'driver'    => $trip->getDriver(),
                    'trip'      => $trip,
                ]
            );
        }

        $em->flush();

        $this->addFlash('success', 'Le trajet est terminé. Les passagers vont être notifiés.');

        return $this->redirectToRoute('app_trip_driver_list');
    }
}