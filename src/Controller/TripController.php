<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/trip')]
final class TripController extends AbstractController
{
    #[Route('/create', name: 'app_trip_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $trip = new Trip();
        $form = $this->createForm(TripForm::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sécurité : empêcher qu'un user choisisse une voiture qu'il ne possède pas
            if ($trip->getCar()->getUser() !== $user) {
                $this->addFlash('danger', 'Vous ne pouvez sélectionner que vos propres véhicules.');
                return $this->redirectToRoute('app_trip_create');
            }

            $em->persist($trip);
            $em->flush();

            $this->addFlash('success', 'Le trajet a bien été enregistré.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trip/create.html.twig', [
            'form' => $form,
        ]);
    }
}
