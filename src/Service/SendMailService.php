<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendMailService
{
    public function __construct(protected MailerInterface $mailer, protected string $defaultFrom) {}

    public function sendMail(
        string $name,
        string $to,
        string $subject,
        string $template,
        array $context
    ) {
        $email = new TemplatedEmail();
        $email->from(new Address($this->defaultFrom, $name))
            ->to($to)
            ->htmlTemplate("emails/$template.html.twig")
            ->subject($subject)
            ->context($context);

        $this->mailer->send($email);
    }
}
