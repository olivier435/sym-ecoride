<?php

namespace App\Controller\Trip;

use App\Entity\Trip;

trait TripContextTrait
{
    protected function getTripContext(Trip $trip, ?string $slug = null, array $extra = []): array
    {
        $driver = $trip->getDriver();
        return array_merge([
            'trip' => $trip,
            'slug' => $slug,
            'travelPreference' => $driver->getTravelPreference(),
            'isFull' => $trip->isFull(),
            'placesLeft' => $trip->getSeatsLeft(),
        ], $extra);
    }
}