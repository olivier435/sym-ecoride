<?php

namespace App\EventSubscriber;

use App\Event\ContactSuccessEvent;
use App\Service\SendMailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactEmailSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(protected SendMailService $sendMailService, protected string $defaultFrom)
    {}

    public static function getSubscribedEvents()
    {
        return [
            'contact.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(ContactSuccessEvent $contactSuccessEvent): void
    {
        $contact = $contactSuccessEvent->getContact();
        $this->sendMailService->sendMail(
            'Demande de contact',
            $this->defaultFrom,
            'Demande de contact',
            'contact',
            ['contact' => $contact]
        );
    }
}