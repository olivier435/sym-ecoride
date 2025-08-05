<?php

namespace App\Service;

final class CityExtractor
{
    /**
     * Extrait la ville depuis une adresse complète.
     * Exemple : "5 Boulevard Vincent Gâche, 44200 NANTES" → "NANTES"
     */
    public function extractFromAddress(?string $address): ?string
    {
        if (!$address) {
            return null;
        }

        // On cherche une ville après un code postal à 5 chiffres
        if (preg_match('/\b\d{5}\s+([A-Za-zÀ-ÿ\-\' ]+)/u', $address, $matches)) {
            return trim($matches[1]);
        }

        // Fallback : on prend le dernier morceau après la dernière virgule
        $parts = explode(',', $address);
        return trim(end($parts));
    }
}