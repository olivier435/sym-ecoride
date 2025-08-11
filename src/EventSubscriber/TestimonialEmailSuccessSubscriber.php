<?php

namespace App\EventSubscriber;

use App\Event\TestimonialSuccessEvent;
use App\Service\SendMailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestimonialEmailSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(protected SendMailService $sendMailService)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            'testimonial.success' => 'sendTestimonialEmail'
        ];
    }

    public function sendTestimonialEmail(TestimonialSuccessEvent $testimonialSuccessEvent)
    {
        $testimonial = $testimonialSuccessEvent->getTestimonial();

        $this->sendMailService->sendMail(
            'Demande de validation',
            'service-commercial@ecoride.com', // A modifier en fonction de l'environnement du client
            'Demande de validation de témoignage',
            'testimonial',
            [
                'testimonial' => $testimonial
            ]
        );
    }
}