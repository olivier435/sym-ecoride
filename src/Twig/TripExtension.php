<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use DateTimeInterface;

class TripExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('duration', [$this, 'formatDuration']),
        ];
    }

    public function formatDuration(DateTimeInterface $start, DateTimeInterface $end): string
    {
        if ($end < $start) {
            return '';
        }

        $interval = $start->diff($end);

        $hours = $interval->h + ($interval->d * 24);
        $minutes = $interval->i;

        return sprintf('%dh%02d', $hours, $minutes);
    }
}