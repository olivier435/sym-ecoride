<?php

namespace App\EventSubscriber;

use App\Event\ComplaintSuccessEvent;
use App\Service\SendMailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ComplaintEmailSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(protected SendMailService $sendMailService)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            ComplaintSuccessEvent::NAME => 'onComplaintSuccess',
        ];
    }

    public function onComplaintSuccess(ComplaintSuccessEvent $event): void
    {
        $complaint = $event->getComplaint();
        $tripPassenger = $complaint->getTripPassenger();
        $trip = $tripPassenger->getTrip();
        $passenger = $tripPassenger->getUser();
        $driver = $trip->getDriver();

        // Infos nécessaires pour l'email
        $data = [
            'type'          => $complaint->getType()?->label() ?? (string) $complaint->getType(),
            'comment'       => $complaint->getComment(),
            'passenger'     => [
                'pseudo'  => $passenger->getPseudo(),
                'prenom'  => $passenger->getFirstname(),
                'nom'     => $passenger->getLastname(),
                'email'   => $passenger->getEmail(),
            ],
            'driver'        => [
                'pseudo'  => $driver->getPseudo(),
                'prenom'  => $driver->getFirstname(),
                'nom'     => $driver->getLastname(),
                'email'   => $driver->getEmail(),
            ],
            'trip'          => [
                'departureDate' => $trip->getDepartureDate(),
                'arrivalDate'   => $trip->getArrivalDate(),
                'departureAddress' => $trip->getDepartureAddress(),
                'arrivalAddress'   => $trip->getArrivalAddress(),
            ]
        ];

        // Tu peux utiliser un template Twig dans ton service d'envoi
        $this->sendMailService->sendMail(
            'Litige soumis',
            'service-commercial@ecoride.com',
            'soumission d\'un litige concernant un trajet : ' . ($data['type'] ?? 'Autre'),
            'complaint_report',
            $data
        );
    }
}