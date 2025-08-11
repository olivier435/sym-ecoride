<?php

namespace App\Controller\Trip;

use App\Entity\Trip;
use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trip')]
final class TripPublicController extends AbstractController
{
    use TripContextTrait;

    #[Route('/detail/{id}-{slug}', name: 'app_trip_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Trip $trip, string $slug, SluggerInterface $slugger, Request $request, TestimonialRepository $testimonialRepository)
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

        $driver = $trip->getDriver();
        $avgRating = $testimonialRepository->getAvgRatingsForDriver($driver->getId());
        $totalCount = $testimonialRepository->getTotalCountForDriver($driver->getId());

        $context = $this->getTripContext($trip, $slug);
        $context['avgRating'] = $avgRating;
        $context['totalCount'] = $totalCount;

        if ($request->isXmlHttpRequest()) {
            return $this->render('trip_public/_detail_partial.html.twig', $context);
        }

        return $this->render('trip_public/detail.html.twig', $context);
    }
}