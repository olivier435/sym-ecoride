<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripDepartureDateFormType;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/departure-date', name: 'app_trip_wizard_departure_date')]
#[IsGranted('ROLE_USER')]
final class DepartureDateController extends AbstractController
{
    public function __invoke(Request $request, TripCreationStorage $storage): Response
    {
        $form = $this->createForm(TripDepartureDateFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $storage->saveStepData(['departureDate' => $data['departureDate']]);

            return $this->redirectToRoute('app_trip_wizard_departure_time');
        }

        return $this->render('trip_wizard/departure_date.html.twig', [
            'form' => $form,
        ]);
    }
}