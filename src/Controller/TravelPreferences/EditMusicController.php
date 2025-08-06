<?php

namespace App\Controller\TravelPreferences;

use App\Form\TravelPreference\TravelPreferenceMusicType;
use App\Service\TravelPreferenceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/preferences/edit/music', name: 'app_travel_preference_edit_music', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_USER')]
final class EditMusicController extends AbstractController
{
    public function __invoke(
        Request $request,
        TravelPreferenceManager $manager,
        EntityManagerInterface $em
    ): Response
    {
        $user = $this->getUser();
        $preference = $manager->getOrCreateForUser($user);

        $form = $this->createForm(TravelPreferenceMusicType::class, $preference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($preference);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->render('travel_preference/_recap.html.twig', ['preference' => $preference]);
            }

            return $this->redirectToRoute('app_travel_preference_index');
        }

        return $this->render('travel_preference/_form_music.html.twig', [
            'form' => $form,
            'action' => $this->generateUrl('app_travel_preference_edit_music'),
        ]);
    }
}