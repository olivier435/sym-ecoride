<?php

namespace App\Service;

use App\Entity\TravelPreference;
use App\Entity\User;
use App\Enum\DiscussionPreference;
use App\Enum\MusicPreference;
use App\Enum\PetPreference;
use App\Enum\SmokingPreference;
use App\Repository\TravelPreferenceRepository;

class TravelPreferenceManager
{
    public function __construct(private readonly TravelPreferenceRepository $repo) {}

    public function getOrCreateForUser(User $user): TravelPreference
    {
        $preference = $this->repo->findOneBy(['user' => $user]);
        if (!$preference) {
            $preference = new TravelPreference();
            $preference->setUser($user);
            $preference->setDiscussion(DiscussionPreference::default());
            $preference->setMusic(MusicPreference::default());
            $preference->setSmoking(SmokingPreference::default());
            $preference->setPets(PetPreference::default());
        }
        return $preference;
    }
}