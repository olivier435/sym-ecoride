<?php

namespace App\Enum;

enum DiscussionPreference: string
{
    case TALKATIVE = 'talkative';
    case DEPENDS = 'depends';
    case QUIET = 'quiet';

    public function label(): string
    {
        return match ($this) {
            self::TALKATIVE => "Je suis un vrai moulin à paroles !",
            self::DEPENDS => "J'aime bien discuter quand je me sens à l'aise",
            self::QUIET => "Je suis plutôt du genre discret",
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TALKATIVE => '💬',
            self::DEPENDS => '🙂',
            self::QUIET => '🤫',
        };
    }

    // public function svg(): string
    // {
    //     return match ($this) {
    //         self::TALKATIVE => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w9swoz ejccx3jq ejccx3f ejccx3jq ejccx3f"><g color="neutralTxtModerate"><g color="currentColor"><path fill="currentColor" fill-rule="evenodd" d="M6.5 9.25a.75.75 0 0 1 .75.75v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 1 .75-.75M17.5 9.25a.75.75 0 0 1 .75.75v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 1 .75-.75M9.25 8.25A.75.75 0 0 1 10 9v4a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75M14.75 8.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75M12 7.25a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0V8a.75.75 0 0 1 .75-.75" clip-rule="evenodd"></path><path fill="currentColor" fill-rule="evenodd" d="M5.323 15.26v3.625l3.308-1.65.588.195c.294.097 1.185.34 2.781.34 4.999 0 8.5-3.244 8.5-6.634S16.999 4.5 12 4.5s-8.5 3.245-8.5 6.636c0 1.34.515 2.616 1.457 3.702zm3.423 3.593c.45.15 1.502.418 3.254.418 5.523 0 10-3.642 10-8.135S17.523 3 12 3 2 6.642 2 11.136c0 1.744.675 3.36 1.823 4.684v4.682a.5.5 0 0 0 .724.447z" clip-rule="evenodd"></path></g></g></svg>',
    //         self::DEPENDS => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w9swoz ejccx3jq ejccx3f ejccx3jq ejccx3f"><g color="neutralTxtModerate"><g color="currentColor"><path fill="currentColor" fill-rule="evenodd" d="M8.75 9.25a.75.75 0 0 1 .75.75v2A.75.75 0 0 1 8 12v-2a.75.75 0 0 1 .75-.75M15.25 9.25A.75.75 0 0 1 16 10v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 1 .75-.75M12 8.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75" clip-rule="evenodd"></path><path fill="currentColor" fill-rule="evenodd" d="M5.323 15.26v3.625l3.308-1.65.588.195c.294.097 1.185.34 2.781.34 4.999 0 8.5-3.244 8.5-6.634S16.999 4.5 12 4.5s-8.5 3.245-8.5 6.636c0 1.34.515 2.616 1.457 3.702zm3.423 3.593c.45.15 1.502.418 3.254.418 5.523 0 10-3.642 10-8.135S17.523 3 12 3 2 6.642 2 11.136c0 1.744.675 3.36 1.823 4.684v4.682a.5.5 0 0 0 .724.447z" clip-rule="evenodd"></path></g></g></svg>',
    //         self::QUIET   => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w9swoz ejccx3jq ejccx3f ejccx3jq ejccx3f"><g color="neutralTxtModerate"><g color="currentColor"><path fill="currentColor" d="M13 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0M17 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0M9 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path><path fill="currentColor" fill-rule="evenodd" d="M5.323 15.26v3.625l3.308-1.65.588.195c.294.097 1.185.34 2.781.34 4.999 0 8.5-3.244 8.5-6.634S16.999 4.5 12 4.5s-8.5 3.245-8.5 6.636c0 1.34.515 2.616 1.457 3.702zm3.423 3.593c.45.15 1.502.418 3.254.418 5.523 0 10-3.642 10-8.135S17.523 3 12 3 2 6.642 2 11.136c0 1.744.675 3.36 1.823 4.684v4.682a.5.5 0 0 0 .724.447z" clip-rule="evenodd"></path></g></g></svg>',
    //     };
    // }

    public static function labelField(): string{
        return "Discussion";
    }

    public static function default(): self
    {
        return self::DEPENDS;
    }
}