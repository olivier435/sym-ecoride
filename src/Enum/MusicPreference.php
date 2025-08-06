<?php

namespace App\Enum;

enum MusicPreference: string
{
    case MUSIC_ALL_ALONG = 'music_all_along';
    case DEPENDS = 'depends';
    case SILENT = 'silent';

    public function label(): string
    {
        return match ($this) {
            self::MUSIC_ALL_ALONG => "Musique tout le long !",
            self::DEPENDS => "Ça dépend de la musique",
            self::SILENT => "Le silence est d'or !",
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MUSIC_ALL_ALONG => '🎶',
            self::DEPENDS => '🎵',
            self::SILENT => '🤫',
        };
    }

    public static function labelField(): string
    {
        return "Musique";
    }

    public static function default(): self
    {
        return self::DEPENDS;
    }
}