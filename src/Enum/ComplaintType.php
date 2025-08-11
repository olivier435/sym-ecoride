<?php

namespace App\Enum;

enum ComplaintType: string
{
    case TRIP_NOT_PERFORMED = 'trip_not_performed';      // Trajet non effectué
    case IMPOSSIBLE_TO_CANCEL = 'impossible_to_cancel';  // Impossible d'annuler
    case PROBLEM_ON_TRIP = 'problem_on_trip';            // Problème survenu

        public function label(): string
    {
        return match ($this) {
            self::TRIP_NOT_PERFORMED => "Vous n'avez pas effectué le trajet que vous aviez prévu avec un⋅e conducteur⋅rice, et celui-ci ou celle-ci n'a pas annulé le trajet depuis son Profil",
            self::IMPOSSIBLE_TO_CANCEL => "Vous n'avez pas pu annuler votre réservation sur le site avant le départ",
            self::PROBLEM_ON_TRIP => "Vous souhaitez signaler un problème survenu lors du trajet",
        };
    }
}