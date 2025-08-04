<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripArrivalDateFormType;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/arrival-date', name: 'app_trip_wizard_arrival_date')]
#[IsGranted('ROLE_USER')]
final class ArrivalDateController extends AbstractController
{
    use WizardRedirectTrait;

    public function __invoke(Request $request, TripCreationStorage $storage, UrlGeneratorInterface $urlGenerator): Response
    {
        $data = $storage->getData();
        $form = $this->createForm(TripArrivalDateFormType::class, [
            'arrivalDate' => $data['arrivalDate'] ?? null,
            'arrivalTime' => $data['arrivalTime'] ?? null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $storage->saveStepData([
                'arrivalDate' => $data['arrivalDate'],
                'arrivalTime' => $data['arrivalTime'],
            ]);

            return $this->redirectAfterStep('app_trip_wizard_recap', $request, $urlGenerator);
        }

        return $this->render('trip_wizard/arrival_date.html.twig', [
            'form' => $form,
        ]);
    }
}