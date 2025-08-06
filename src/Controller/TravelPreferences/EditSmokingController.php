<?php

namespace App\Controller\TravelPreferences;

use App\Form\TravelPreference\TravelPreferenceSmokingType;
use App\Service\TravelPreferenceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/preferences/edit/smoking', name: 'app_travel_preference_edit_smoking', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_USER')]
final class EditSmokingController extends AbstractController
{
    public function __invoke(
        Request $request,
        TravelPreferenceManager $manager,
        EntityManagerInterface $em
    ): Response
    {
        $user = $this->getUser();
        $preference = $manager->getOrCreateForUser($user);

        $form = $this->createForm(TravelPreferenceSmokingType::class, $preference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($preference);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->render('travel_preference/_recap.html.twig', ['preference' => $preference]);
            }

            return $this->redirectToRoute('app_travel_preference_index');
        }

        return $this->render('travel_preference/_form_smoking.html.twig', [
            'form' => $form,
            'action' => $this->generateUrl('app_travel_preference_edit_smoking'),
        ]);
    }
}