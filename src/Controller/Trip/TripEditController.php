<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripForm;
use App\Service\CityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/trip')]
final class TripEditController extends AbstractController
{
    #[Route('/edit/{id}', name: 'app_trip_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Trip $trip, Request $request, EntityManagerInterface $em, CityManager $cityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérifie que l'utilisateur est bien le conducteur
        if ($trip->getDriver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce trajet.');
        }

        if (!$trip->isEditable()) {
            $this->addFlash('warning', 'Vous ne pouvez modifier que les trajets à venir sans passager, ou annulés.');
            return $this->redirectToRoute('app_trip_driver_list');
        }

        $form = $this->createForm(TripForm::class, $trip, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($trip->getCar()->getUser() !== $user) {
                $this->addFlash('danger', 'Vous ne pouvez sélectionner que vos propres véhicules.');
                return $this->redirectToRoute('app_trip_edit', ['id' => $trip->getId()]);
            }

            if ($trip->getStatus() === Trip::STATUS_CANCELLED) {
                $trip->setStatus(Trip::STATUS_UPCOMING);
            }
            $trip->setDepartureCity($cityManager->getOrCreateCity($trip->getDepartureAddress()))
                ->setArrivalCity($cityManager->getOrCreateCity($trip->getArrivalAddress()));
            $em->flush();
            $cityManager->purgeOrphanCities();
            $this->addFlash('success', 'Le trajet a bien été mis à jour.');
            return $this->redirectToRoute('app_trip_driver_detail', [
                'id' => $trip->getId(),
            ]);
        }

        return $this->render('trip/edit.html.twig', [
            'form' => $form,
            'trip' => $trip,
        ]);
    }
}