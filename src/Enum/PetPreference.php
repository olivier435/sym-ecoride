<?php

namespace App\Enum;

enum PetPreference: string
{
    case LOVES_PETS = 'loves_pets';
    case SOME_PETS = 'some_pets';
    case NO_PETS = 'no_pets';

    public function label(): string
    {
        return match ($this) {
            self::LOVES_PETS => "J'adore les animaux. Ouaf !",
            self::SOME_PETS => "Je peux voyager avec certains animaux",
            self::NO_PETS => "Je préfère ne pas voyager en compagnie d'animaux",
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LOVES_PETS => '🐾',
            self::SOME_PETS => '🐕',
            self::NO_PETS => '🚫🐾',
        };
    }

    public static function labelField(): string
    {
        return "Animaux";
    }

    public static function default(): self
    {
        return self::SOME_PETS;
    }
}