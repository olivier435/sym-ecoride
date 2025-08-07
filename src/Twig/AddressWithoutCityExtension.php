<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AddressWithoutCityExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('withoutCity', [$this, 'withoutCity']),
        ];
    }

    public function withoutCity(?string $address): ?string
    {
        if (!$address) return $address;
        $parts = explode(',', $address);
        return trim($parts[0]);
    }
}