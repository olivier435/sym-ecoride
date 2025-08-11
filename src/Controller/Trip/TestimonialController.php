<?php

namespace App\Controller\Trip;

use App\Entity\Testimonial;
use App\Entity\Trip;
use App\Entity\User;
use App\Event\TestimonialSuccessEvent;
use App\Form\TestimonialType;
use App\Repository\TestimonialRepository;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class TestimonialController extends AbstractController
{
    #[Route('/trip/{id}/testimonial/new', name: 'app_trip_passenger_testimonial_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Trip $trip, EntityManagerInterface $em, EventDispatcherInterface $dispatcher): Response
    {
        $user = $this->getUser();

        // Sécurité : Vérifier que l'utilisateur est bien passager de ce trip
        $isPassenger = $trip->getPassengers()->exists(fn($_, $u) => $u === $user);
        if (!$isPassenger) {
            throw $this->createAccessDeniedException();
        }

        // Empêcher plusieurs avis pour ce trip/user (normalement contrôlé en base mais on double ici)
        foreach ($trip->getTestimonials() as $testimonial) {
            if ($testimonial->getAuthor() === $user) {
                $this->addFlash('info', 'Vous avez déjà laissé un avis pour ce trajet.');
                return $this->redirectToRoute('app_trip_passenger_list');
            }
        }

        $testimonial = new Testimonial();
        $testimonial->setTrip($trip)
            ->setAuthor($user)
            ->setRating(5);

        $form = $this->createForm(TestimonialType::class, $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($testimonial);
            $em->flush();

            // Envoi email au service client
            $testimonialEvent = new TestimonialSuccessEvent($testimonial);
            $dispatcher->dispatch($testimonialEvent, 'testimonial.success');

            $this->addFlash('success', 'Merci pour votre avis ! Il sera publié après validation.');
            return $this->redirectToRoute('app_trip_passenger_list');
        }

        return $this->render('testimonial/new.html.twig', [
            'form' => $form,
            'trip' => $trip,
        ]);
    }

    #[Route('/driver/{id}/testimonials', name: 'app_driver_testimonials')]
    public function driverTestimonials(User $driver, TestimonialRepository $testimonialRepository, RatingService $ratingService): Response
    {
        // Récupère les avis approuvés pour ce conducteur
        $testimonials = $testimonialRepository->findApprovedForDriver($driver->getId());

        // Moyenne et total pour ce conducteur
        $avgRating = $testimonialRepository->getAvgRatingsForDriver($driver->getId());
        $totalCount = $testimonialRepository->getTotalCountForDriver($driver->getId());

        // Stats par note pour ce conducteur
        $percentages = $ratingService->calculateRatingPercentages($testimonials);

        return $this->render('testimonial/index.html.twig', [
            'testimonials' => $testimonials,
            'avgRating' => $avgRating,
            'totalCount' => $totalCount,
            'percentages' => $percentages,
            'driver' => $driver,
        ]);
    }
}