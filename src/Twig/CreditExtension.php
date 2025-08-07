<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CreditExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('credit', [$this, 'formatCredit'], ['is_safe' => ['html']]),
        ];
    }

    public function formatCredit(int $value, bool $withIcon = false): string
    {
        $label = ($value === 1) ? 'crédit' : 'crédits';
        $str = $value . ' ' . $label;
        if ($withIcon) {
            $str = '<i class="bi bi-coin"></i> ' . $str;
        }
        return $str;
    }
}