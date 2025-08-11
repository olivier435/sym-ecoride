<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TripStatusExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('trip_status_badge', [$this, 'getTripStatusBadge'], ['is_safe' => ['html']]),
        ];
    }

    public function getTripStatusBadge(?string $status): string
    {
        return match ($status) {
            'à venir' => <<<HTML
            <svg viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="x3fx3u" width="20" height="20">
                <g color="currentColor"><g color="currentColor">
                <path fill="currentColor" fill-rule="evenodd" d="M12 20.5a8.5 8.5 0 1 0 0-17 8.5 8.5 0 0 0 0 17m0 1.5c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10" clip-rule="evenodd"/>
                <path fill="currentColor" fill-rule="evenodd" d="M12 7a1 1 0 0 1 1 1v3.15l2.13 1.23a1 1 0 1 1-1 1.74l-2.5-1.44A1 1 0 0 1 11 12V8a1 1 0 0 1 1-1z" clip-rule="evenodd"/>
                </g></g>
            </svg>
            <span>À venir</span>
            HTML,
            'en cours' => <<<HTML
            <svg viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="x3fx3u" width="20" height="20">
                <g color="currentColor">
                    <g color="currentColor">
                        <path fill="currentColor" fill-rule="evenodd" d="M12 20.5a8.5 8.5 0 1 0 0-17 8.5 8.5 0 0 0 0 17m0 1.5c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10" clip-rule="evenodd"/>
                        <path fill="currentColor" fill-rule="evenodd" d="M15.5 8.5a4 4 0 1 0 1.23 4.1.75.75 0 1 1 1.42.42A5.5 5.5 0 1 1 17 7.94V7a.75.75 0 1 1 1.5 0v2.25a.75.75 0 0 1-.75.75H15a.75.75 0 1 1 0-1.5h.5z" clip-rule="evenodd"/>
                    </g>
                </g>
            </svg>
            <span>En cours</span>
            HTML,
            'effectué' => <<<HTML
            <svg viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="x3fx3u me-1" width="20" height="20">
                <g color="currentColor">
                    <g color="currentColor">
                        <path fill="currentColor" fill-rule="evenodd" d="M12 20.5a8.5 8.5 0 1 0 0-17 8.5 8.5 0 0 0 0 17m0 1.5c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10" clip-rule="evenodd"/>
                        <path fill="currentColor" fill-rule="evenodd" d="M9.5 13.5a1 1 0 0 1 1.42 0l1.09 1.08 2.09-2.08a1 1 0 0 1 1.41 1.41l-2.8 2.79a1 1 0 0 1-1.41 0l-1.78-1.77a1 1 0 0 1 0-1.43z" clip-rule="evenodd"/>
                    </g>
                </g>
               
            </svg>
            <span>Terminé</span>
            HTML,
            'annulé' => <<<HTML
            <svg viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="x3fx3u" width="20" height="20">
                <g color="currentColor">
                    <g color="currentColor">
                        <path fill="currentColor" fill-rule="evenodd" d="M12 20.5a8.5 8.5 0 1 0 0-17 8.5 8.5 0 0 0 0 17m0 1.5c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10" clip-rule="evenodd"></path>
                        <path fill="currentColor" fill-rule="evenodd" d="M16.5 12a4.5 4.5 0 0 1-5.416 4.407l5.323-5.323q.091.444.093.916m-7.587 4.456c-.218.218-.189.581.078.736a6 6 0 0 0 8.201-8.201c-.155-.267-.518-.296-.736-.078zm6.035-9.683c.27.153.302.519.082.738l-7.519 7.52c-.22.219-.585.188-.738-.083a6 6 0 0 1 8.175-8.175m-7.315 6.318 5.458-5.458a4.5 4.5 0 0 0-5.458 5.458" clip-rule="evenodd"></path>
                    </g>
                </g>
            </svg>
            <span>Annulé</span>
            HTML,
            'validé' => <<<HTML
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="x3fx3u me-1" width="20" height="20">
                <g color="currentColor">
                    <g color="currentColor">
                        <circle cx="12" cy="12" r="10" fill="#2ecc71"/>
                        <path d="M17 8.5l-5.5 7-2.5-2.5" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </g>
            </svg>
            <span>Validé</span>
            HTML,
            'signalé' => <<<HTML
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="x3fx3u me-1" width="20" height="20">
                <g color="currentColor">
                    <g color="currentColor">
                        <circle cx="12" cy="12" r="10" fill="#e74c3c"/>
                        <path d="M12 8v4" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="16" r="1" fill="#fff"/>
                    </g>
                </g>
            </svg>
            <span>Signalé</span>
            HTML,
            default => '',
        };
    }
}