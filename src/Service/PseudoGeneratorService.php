<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class PseudoGeneratorService
{
    public function __construct(private UserRepository $userRepository, private SluggerInterface $slugger) {}

    public function generate(string $firstname, string $lastname): string
    {
        // Base du pseudo : prenom.nom (tout en minuscule, sans accents)
        $base = strtolower($this->slugger->slug($firstname . '-' . $lastname));

        // Ajout 3 chiffres aléatoires
        $pseudo = $base . random_int(100, 999);

        // Vérification unicité
        while ($this->userRepository->findOneBy(['pseudo' => $pseudo])) {
            $pseudo = $base . random_int(100, 999);
        }

        return $pseudo;
    }
}