<?php

namespace App\Controller\TripWizard;

use App\Form\Trip\TripPriceFormType;
use App\Service\TripCreationStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip/create/price', name: 'app_trip_wizard_price')]
#[IsGranted('ROLE_USER')]
final class PriceController extends AbstractController
{
    public function __invoke(Request $request, TripCreationStorage $storage): Response
    {
        $data = $storage->getData();

        $form = $this->createForm(TripPriceFormType::class, [
            'pricePerPerson' => $data['pricePerPerson'] ?? 1000, // par défaut : 10 €
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $storage->saveStepData(['pricePerPerson' => $formData['pricePerPerson']]);

            return $this->redirectToRoute('app_trip_wizard_vehicle');
        }

        return $this->render('trip_wizard/price.html.twig', [
            'form' => $form,
        ]);
    }
}