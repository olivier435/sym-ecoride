<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripDepartureFormType;
use App\Service\AddressFormatter;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/departure', name: 'app_trip_wizard_departure')]
#[IsGranted('ROLE_USER')]
final class DepartureController extends AbstractController
{
    public function __invoke(Request $request, TripCreationStorage $storage, AddressFormatter $formatter): Response
    {
        $form = $this->createForm(TripDepartureFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $formattedAddress = $formatter->format($data['departureAddress']);

            $storage->saveStepData([
                'departureAddress' => $formattedAddress,
            ]);

            return $this->redirectToRoute('app_trip_wizard_arrival');
        }

        return $this->render('trip_wizard/departure.html.twig', [
            'form' => $form,
        ]);
    }
}