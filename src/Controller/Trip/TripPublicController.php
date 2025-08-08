<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trip')]
final class TripPublicController extends AbstractController
{
    use TripContextTrait;

    #[Route('/detail/{id}-{slug}', name: 'app_trip_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip, string $slug, SluggerInterface $slugger, Request $request)
    {
        if (!$trip || !in_array($trip->getStatus(), Trip::STATUSES, true)) {
            throw $this->createNotFoundException('Ce covoiturage n\'existe pas.');
        }

        // Vérification du slug et redirection si mauvais slug
        $expectedSlug = $slugger->slug($trip->getSlugSource())->lower();

        if ($slug !== (string) $expectedSlug) {
            return $this->redirectToRoute('app_trip_detail', [
                'id' => $trip->getId(),
                'slug' => (string) $expectedSlug,
            ]);
        }

        // Prépare la liste d'avis (à implémenter plus tard)
        // $reviews = $reviewRepository->findBy(['driver' => $driver], ['createdAt' => 'DESC']);

        if ($request->isXmlHttpRequest()) {
            return $this->render('trip_public/_detail_partial.html.twig', $this->getTripContext($trip, $slug));
        }

        return $this->render('trip_public/detail.html.twig', $this->getTripContext($trip, $slug));
    }
}