<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/trip')]
final class TripController extends AbstractController
{
    #[Route('/create', name: 'app_trip_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User */
        $user = $this->getUser();

        $trip = new Trip();
        $trip->setDriver($user); // on assigne directement le conducteur

        $form = $this->createForm(TripForm::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sécurité : empêcher qu'un user choisisse une voiture qu'il ne possède pas
            if ($trip->getCar()->getUser() !== $user) {
                $this->addFlash('danger', 'Vous ne pouvez sélectionner que vos propres véhicules.');
                return $this->redirectToRoute('app_trip_create');
            }

            $em->persist($trip);
            $em->flush();

            $this->addFlash('success', 'Le trajet a bien été enregistré !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trip/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_trip_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Trip $trip, Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérifie que l'utilisateur est bien le conducteur
        if ($trip->getDriver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\êtes pas autorisé à modifier ce trajet.');
        }

        // Vérifie que la date de départ est dans le futur
        if ($trip->getDepartureDate() < new \DateTimeImmutable('today')) {
            $this->addFlash('warning', 'Vous ne pouvez pas modifier un trajet dont la date est passée.');
            return $this->redirectToRoute('app_trip_driver_list');
        }

        $form = $this->createForm(TripForm::class, $trip, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sécurité sur la voiture
            if ($trip->getCar()->getUser() !== $user) {
                $this->addFlash('danger', 'Vous ne pouvez sélectionner que vos propres véhicules.');
                return $this->redirectToRoute('app_trip_edit', ['id' => $trip->getId()]);
            }

            $em->flush();

            $this->addFlash('success', 'Le trajet a bien été mis à jour.');
            return $this->redirectToRoute('app_trip_driver_list');
        }

        return $this->render('trip/edit.html.twig', [
            'form' => $form,
            'trip' => $trip,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_trip_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trip $trip, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($trip->getDriver() !== $user) {
            return new JsonResponse(['error' => 'Accès interdit'], Response::HTTP_FORBIDDEN);
        }

        if (!$trip->isDeletable()) {
            return new JsonResponse(['error' => 'Ce trajet ne peut pas être supprimé.'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->isCsrfTokenValid('delete' . $trip->getId(), $request->request->get('_token'))) {
            $em->remove($trip);
            $em->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['error' => 'Token invalide'], Response::HTTP_FORBIDDEN);
    }
}