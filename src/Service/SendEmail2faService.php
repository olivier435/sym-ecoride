<?php

namespace App\Service;

use App\Entity\User;
use App\Service\SendMailService;
use Symfony\Bundle\SecurityBundle\Security;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

class SendEmail2faService implements AuthCodeMailerInterface
{
    public function __construct(protected SendMailService $email, protected Security $security)
    {}

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();

        // Envoi de l'email
        /** @var User */
        $user = $this->security->getUser();
        $this->email->sendMail(
            'Code de vérification : application Ecoride',
            $user->getEmail(),
            'Code de vérification',
            'authentication',
            [
                'user' => $user,
                'authCode' => $authCode
            ]
        );
    }
}