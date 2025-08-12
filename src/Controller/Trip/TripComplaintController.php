<?php

namespace App\Controller\Trip;

use App\Entity\Complaint;
use App\Entity\Trip;
use App\Event\ComplaintSuccessEvent;
use App\Form\ComplaintFormType;
use App\Repository\TripPassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mes-trajets/passager/complaint')]
#[IsGranted('ROLE_USER')]
final class TripComplaintController extends AbstractController
{
    #[Route('/{id}', name: 'app_trip_passenger_complaint', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function create(Request $request, Trip $trip, TripPassengerRepository $repo, EntityManagerInterface $em, EventDispatcherInterface $dispatcher): Response
    {
        $user = $this->getUser();
        $tripPassenger = $repo->findOneBy(['trip' => $trip, 'user' => $user]);
        if (!$tripPassenger) {
            throw $this->createAccessDeniedException();
        }

        // Si une réclamation existe déjà, on l'affiche simplement !
        if ($tripPassenger->getComplaint()) {
            // Rendu AJAX ou normal
            if ($request->isXmlHttpRequest()) {
                return $this->render('trip_complaint/_recap.html.twig', [
                    'complaint' => $tripPassenger->getComplaint(),
                    'trip' => $trip,
                ]);
            }
            return $this->render('trip_complaint/recap.html.twig', [
                'complaint' => $tripPassenger->getComplaint(),
                'trip' => $trip,
            ]);
        }

        $complaint = new Complaint();
        $form = $this->createForm(ComplaintFormType::class, $complaint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complaint->setTripPassenger($tripPassenger);
            $tripPassenger->setComplaint($complaint);
            $tripPassenger->setValidationStatus('reported');
            $tripPassenger->setValidationAt(new \DateTimeImmutable());
            $em->persist($complaint);
            $em->persist($tripPassenger);
            $em->flush();

            $complaintEvent = new ComplaintSuccessEvent($complaint);
            $dispatcher->dispatch($complaintEvent, ComplaintSuccessEvent::NAME);

            if ($request->isXmlHttpRequest()) {
                return $this->render('trip_complaint/_recap.html.twig', [
                    'complaint' => $complaint,
                    'trip' => $trip,
                ]);
            }

            $this->addFlash('success', "Votre réclamation a été transmise à notre équipe.");
            // Ici, ON NE REDIRIGE PAS vers cette page (sinon boucle) mais plutôt vers la liste des trajets, ou la page de détail du trajet :
            return $this->redirectToRoute('app_trip_passenger_complaint', ['id' => $trip->getId()]);
        }

        // Rendu AJAX ou normal
        if ($request->isXmlHttpRequest()) {
            return $this->render('trip_complaint/_form.html.twig', [
                'form' => $form->createView(),
                'trip' => $trip,
            ]);
        }

        return $this->render('trip_complaint/edit.html.twig', [
            'form' => $form->createView(),
            'trip' => $trip,
        ]);
    }
}