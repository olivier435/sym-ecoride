<?php

namespace App\Controller;

use App\Form\Trip\TripDepartureFormType;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create')]
final class TripWizardController extends AbstractController
{
    #[Route('/departure', name: 'app_trip_wizard_departure')]
    #[IsGranted('ROLE_USER')]
    public function departure(Request $request, TripCreationStorage $storage): Response
    {
        $form = $this->createForm(TripDepartureFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $storage->saveStepData([
                'departureAddress' => $data['departureAddress'],
            ]);

            // return $this->redirectToRoute('app_trip_wizard_arrival');
        }
        return $this->render('trip_wizard/departure.html.twig', [
            'form' => $form,
        ]);
    }
}