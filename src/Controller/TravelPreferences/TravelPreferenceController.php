<?php

namespace App\Controller\TravelPreferences;

use App\Repository\TravelPreferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/preferences', name: 'app_travel_preference_')]
#[IsGranted('ROLE_USER')]
final class TravelPreferenceController extends AbstractController
{
    #[Route('', name: 'index')]
    public function __invoke(Request $request, TravelPreferenceRepository $repo): Response
    {
        $user = $this->getUser();
        $preference = $repo->findOneBy(['user' => $user]);

        if ($request->isXmlHttpRequest()) {
            // Retourne UNIQUEMENT le _recap pour AJAX
            return $this->render('travel_preference/_recap.html.twig', [
                'preference' => $preference,
            ]);
        }

        return $this->render('travel_preference/index.html.twig', [
            'preference' => $preference,
        ]);
    }
}