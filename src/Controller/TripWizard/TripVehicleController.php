<?php

namespace App\Controller\TripWizard;

use App\Entity\User;
use App\Form\Trip\TripVehiculeFormType;
use App\Repository\CarRepository;
use App\Service\TripCreationStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TripVehicleController extends AbstractController
{
    use WizardRedirectTrait;

    #[Route('/trip/create/vehicle', name: 'app_trip_wizard_vehicle')]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request, TripCreationStorage $storage, CarRepository $carRepository, UrlGeneratorInterface $urlGenerator): Response
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
        $data = $storage->getData();
        $carSelected = null;
        if (isset($data['carId'])) {
            $carSelected = $carRepository->find($data['carId']);
        }
        $form = $this->createForm(TripVehiculeFormType::class, [
            'car' => $carSelected // $carSelected = récupérer Car depuis $data['carId']
        ], [
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
            return $this->redirectAfterStep('app_trip_wizard_arrival_date', $request, $urlGenerator);
        }

        return $this->render('trip_wizard/vehicle.html.twig', [
            'form' => $form,
        ]);
    }
}