<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AddressExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('city', [$this, 'extractCity']),
        ];
    }

    public function extractCity(string $address): ?string
    {
        if (preg_match('/\d{5}\s+(.+)$/u', $address, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}