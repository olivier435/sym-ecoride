<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripDepartureDateFormType;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/departure-date', name: 'app_trip_wizard_departure_date')]
#[IsGranted('ROLE_USER')]
final class DepartureDateController extends AbstractController
{
    use WizardRedirectTrait;

    public function __invoke(Request $request, TripCreationStorage $storage, UrlGeneratorInterface $urlGenerator): Response
    {
        $data = $storage->getData();
        $form = $this->createForm(TripDepartureDateFormType::class, [
            'departureDate' => $data['departureDate'] ?? null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $storage->saveStepData(['departureDate' => $data['departureDate']]);

            return $this->redirectAfterStep('app_trip_wizard_departure_time', $request, $urlGenerator);
        }

        return $this->render('trip_wizard/departure_date.html.twig', [
            'form' => $form,
        ]);
    }
}