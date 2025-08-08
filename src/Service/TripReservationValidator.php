<?php

namespace App\Service;

use App\Entity\Trip;
use App\Entity\User;

class TripReservationValidator
{
    public function validate(Trip $trip, User $user): array
    {
        if ($trip->isFull()) {
            return ['Trajet complet', 400];
        }
        if ($trip->getPassengers()->contains($user)) {
            return ['Vous participez déjà à ce trajet', 400];
        }
        if ($trip->getDriver() === $user) {
            return ['Vous êtes le conducteur du trajet', 400];
        }
        if ($user->getCredit() < $trip->getPricePerPerson()) {
            return ['Vous n\'avez pas assez de crédits', 400];
        }
        return [null, null];
    }
}