<?php

namespace App\Controller\TripWizard;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\CarRepository;
use App\Service\CityExtractor;
use App\Service\TripCreationStorage;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/trip/create/finalize', name: 'app_trip_finalize')]
#[IsGranted('ROLE_USER')]
final class TripFinalizeController extends AbstractController
{
    public function __invoke(TripCreationStorage $storage, EntityManagerInterface $em, CarRepository $carRepository, CityExtractor $cityExtractor)
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = $storage->getData();

        // Vérification de complétude
        $requiredKeys = [
            'departureAddress',
            'arrivalAddress',
            'departureDate',
            'departureTime',
            'arrivalDate',
            'arrivalTime',
            'seatsAvailable',
            'pricePerPerson',
            'carId',
        ];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                $this->addFlash('danger', 'Des informations manquent dans votre trajet.');
                return $this->redirectToRoute('app_trip_wizard_recap');
            }
        }

        // Récupération du véhicule
        $car = $carRepository->find($data['carId']);
        if (!$car || $car->getUser() !== $user) {
            throw $this->createAccessDeniedException('Ce véhicule ne vous appartient pas.');
        }

        // Récupération et conversion sécurisée des dates/heures
        $departureDate = $data['departureDate'] instanceof DateTimeImmutable
            ? $data['departureDate']
            : DateTimeImmutable::createFromMutable($data['departureDate']);

        $arrivalDate = $data['arrivalDate'] instanceof DateTimeImmutable
            ? $data['arrivalDate']
            : DateTimeImmutable::createFromMutable($data['arrivalDate']);

        $departureTime = $data['departureTime'] instanceof DateTimeImmutable
            ? $data['departureTime']
            : DateTimeImmutable::createFromMutable($data['departureTime']);

        $arrivalTime = $data['arrivalTime'] instanceof DateTimeImmutable
            ? $data['arrivalTime']
            : DateTimeImmutable::createFromMutable($data['arrivalTime']);

        // Création du covoiturage
        $trip = new Trip();
        $trip->setDriver($user)
            ->setDepartureAddress($data['departureAddress'])
            ->setArrivalAddress($data['arrivalAddress'])
            ->setDepartureDate($departureDate)
            ->setDepartureTime($departureTime)
            ->setArrivalDate($arrivalDate)
            ->setArrivalTime($arrivalTime)
            ->setSeatsAvailable($data['seatsAvailable'])
            ->setPricePerPerson($data['pricePerPerson'])
            ->setStatus(Trip::STATUS_UPCOMING)
            ->setCar($car)
            ->setDepartureCity($cityExtractor->extractFromAddress($trip->getDepartureAddress()))
            ->setArrivalCity($cityExtractor->extractFromAddress($trip->getArrivalAddress()));

        $em->persist($trip);
        $em->flush();

        // Nettoyage du storage
        $storage->clear();

        $this->addFlash('success', 'Votre trajet a bien été publié !');

        return $this->redirectToRoute('app_trip_driver_list');
    }
}