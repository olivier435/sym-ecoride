<?php

namespace App\Enum;

enum SmokingPreference: string
{
    case ALLOWED = 'allowed';
    case OUTSIDE_ONLY = 'outside_only';
    case FORBIDDEN = 'forbidden';

    public function label(): string
    {
        return match ($this) {
            self::ALLOWED => "Cigarette autorisée",
            self::OUTSIDE_ONLY => "Les pauses cigarette hors de la voiture ne me dérangent pas",
            self::FORBIDDEN => "Pas de cigarette, svp",
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ALLOWED => '🚬',
            self::OUTSIDE_ONLY => '🚭🚗',
            self::FORBIDDEN => '🚭',
        };
    }

    public static function labelField(): string
    {
        return "Cigarette";
    }

    public static function default(): self
    {
        return self::OUTSIDE_ONLY;
    }
}