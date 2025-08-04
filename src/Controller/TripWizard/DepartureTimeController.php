<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripDepartureTimeFormType;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/departure-time', name: 'app_trip_wizard_departure_time')]
#[IsGranted('ROLE_USER')]
final class DepartureTimeController extends AbstractController
{
    use WizardRedirectTrait;

    public function __invoke(Request $request, TripCreationStorage $storage, UrlGeneratorInterface $urlGenerator): Response
    {
        $data = $storage->getData();
        $form = $this->createForm(TripDepartureTimeFormType::class, [
            'departureTime' => $data['departureTime'] ?? null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $storage->saveStepData(['departureTime' => $data['departureTime']]);

            return $this->redirectAfterStep('app_trip_wizard_seats', $request, $urlGenerator);
        }

        return $this->render('trip_wizard/departure_time.html.twig', [
            'form' => $form,
        ]);
    }
}