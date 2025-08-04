<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CarController extends AbstractController
{
    #[Route('/car/create', name: 'app_car_create')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $car = new Car();
        $car->setUser($user);
        $form = $this->createForm(CarForm::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Formatage de la couleur (ex: gris métallisé => Gris Métallisé)
            $color = $form->get('color')->getData();
            $formattedColor = ucwords(mb_strtolower(trim($color)));
            $car->setColor($formattedColor);
            // Normalisation de l'immatriculation (AA123AA -> AA-123-AA)
            $registration = strtoupper($form->get('registration')->getData());
            $registration = preg_replace('/[^A-Z0-9]/', '', $registration); // Supprime tout sauf lettres/chiffres
            if (preg_match('/^([A-Z]{2})(\d{3})([A-Z]{2})$/', $registration, $matches)) {
                $formattedRegistration = sprintf('%s-%s-%s', $matches[1], $matches[2], $matches[3]);
                $car->setRegistration($formattedRegistration);
            }

            $em->persist($car);
            $em->flush();

            $this->addFlash('success', 'Votre véhicule a été enregistré avec succès !');
            // Redirection vers le wizard si flag fromWizard
            if ($request->query->getBoolean('fromWizard')) {
                return $this->redirectToRoute('app_trip_wizard_vehicle');
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('car/index.html.twig', [
            'form' => $form,
        ]);
    }
}