<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripDepartureFormType;
use App\Service\AddressFormatter;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/departure', name: 'app_trip_wizard_departure')]
#[IsGranted('ROLE_USER')]
final class DepartureController extends AbstractController
{
    use WizardRedirectTrait;

    public function __invoke(Request $request, TripCreationStorage $storage, AddressFormatter $formatter, UrlGeneratorInterface $urlGenerator): Response
    {
        $data = $storage->getData();
        $form = $this->createForm(TripDepartureFormType::class, [
            'departureAddress' => $data['departureAddress'] ?? '',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $formattedAddress = $formatter->format($data['departureAddress']);

            $storage->saveStepData([
                'departureAddress' => $formattedAddress,
            ]);

            // return $this->redirectToRoute('app_trip_wizard_arrival');
            return $this->redirectAfterStep('app_trip_wizard_arrival', $request, $urlGenerator);
        }

        return $this->render('trip_wizard/departure.html.twig', [
            'form' => $form,
        ]);
    }
}