<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('amount', [$this, 'formatPrice'], ['is_safe' => ['html']]),
        ];
    }

    public function formatPrice(int $value, string $symbol = '€', string $decsep = ',', string $thousandsep = ' '): string
    {
        $finalValue = $value / 100;
        $finalValue = number_format($finalValue, 2, $decsep, $thousandsep);

        return $finalValue . ' ' . $symbol;
    }
}