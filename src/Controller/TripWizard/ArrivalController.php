<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripArrivalFormType;
use App\Service\AddressFormatter;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArrivalController extends AbstractController
{
    #[Route('/trip/create/arrival', name: 'app_trip_wizard_arrival')]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request, TripCreationStorage $storage, AddressFormatter $formatter): Response
    {
        $data = $storage->getData();

        $form = $this->createForm(TripArrivalFormType::class, [
            'arrivalAddress' => $data['arrivalAddress'] ?? '',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formattedAddress = $formatter->format($form->get('arrivalAddress')->getData());

            $storage->saveStepData(['arrivalAddress' => $formattedAddress]);

            return $this->redirectToRoute('app_trip_wizard_departure_date');
        }

        return $this->render('trip_wizard/arrival.html.twig', [
            'form' => $form,
        ]);
    }
}