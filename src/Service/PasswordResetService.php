<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class PasswordResetService
{
    public function __construct(
        protected TokenGeneratorInterface $tokenGenerator,
        protected EntityManagerInterface $em,
        protected UrlGeneratorInterface $urlGenerator,
        protected SendMailService $email
    )
    {}

    public function processPasswordReset(User $user): void
    {
        // Génération du token
        $token = $this->tokenGenerator->generateToken();
        $now = new \DateTimeImmutable();
        $user->setResetToken($token)
            ->setCreatedTokenAt($now);
        $this->em->flush();

        // Génération de l'URL
        $url = $this->urlGenerator->generate('app_reset_pw', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // On crée les données du mail
        $context = [
            'url' => $url,
            'user' => $user
        ];

        // Envoi de l'email
        $this->email->sendMail(
            'Infos de l\'application sym-Ecoride',
            $user->getEmail(),
            'Réinitialisation du mot de passe',
            'password_reset',
            $context
        );
    }

    public function getUserByResetToken(string $token, UserRepository $userRepository): ?User
    {
        return $userRepository->findOneBy(['resetToken' => $token]);
    }

    public function isTokenExpired(User $user, int $expirationInHours = 1): bool
    {
        $now = new \DateTimeImmutable();
        return $now > $user->getCreatedTokenAt()->modify("+{$expirationInHours} hour");
    }

    public function updatePassword(User $user, string $plainPassword, UserPasswordHasherInterface $hasher): void
    {
        $hashedPassword = $hasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword)
            ->setResetToken(null)
            ->setCreatedTokenAt(null);

        $this->em->flush();
    }
}