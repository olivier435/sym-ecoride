<?php

namespace App\Controller\TripWizard;

use App\Entity\User;
use App\Form\Trip\TripVehiculeFormType;
use App\Service\TripCreationStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class TripVehicleController extends AbstractController
{
    #[Route('/trip/create/vehicle', name: 'app_trip_wizard_vehicle')]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request, TripCreationStorage $storage): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Si l'utilisateur n'a aucun véhicule, redirection immédiate
        if ($user->getCars()->isEmpty()) {
            $this->addFlash('info', 'Vous devez enregistrer un véhicule avant de poursuivre.');
            return $this->redirectToRoute('app_car_create', [
                'fromWizard' => 1
            ]);
        }

        // Sinon, affichage du formulaire de choix du véhicule
        $form = $this->createForm(TripVehiculeFormType::class, null, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $car = $form->get('car')->getData();

            // Vérification de propriété
            if (!$car || $car->getUser() !== $user) {
                throw new AccessDeniedHttpException('Ce véhicule ne vous appartient pas.');
            }

            $storage->saveStepData(['carId' => $car->getId()]);
            return $this->redirectToRoute('app_trip_wizard_arrival_date');
        }

        return $this->render('trip_wizard/vehicle.html.twig', [
            'form' => $form,
        ]);
    }
}