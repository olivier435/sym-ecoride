<?php

namespace App\Entity;

use App\Enum\DiscussionPreference;
use App\Enum\MusicPreference;
use App\Enum\PetPreference;
use App\Enum\SmokingPreference;
use App\Repository\TravelPreferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TravelPreferenceRepository::class)]
class TravelPreference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'travelPreference', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(type: "string", enumType: DiscussionPreference::class)]
    #[Assert\NotNull]
    private ?DiscussionPreference $discussion = null;

    #[ORM\Column(type: "string", enumType: MusicPreference::class)]
    #[Assert\NotNull]
    private ?MusicPreference $music = null;

    #[ORM\Column(type: "string", enumType: SmokingPreference::class)]
    #[Assert\NotNull]
    private ?SmokingPreference $smoking = null;

    #[ORM\Column(type: "string", enumType: PetPreference::class)]
    #[Assert\NotNull]
    private ?PetPreference $pets = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDiscussion(): ?DiscussionPreference
    {
        return $this->discussion;
    }

    public function setDiscussion(DiscussionPreference $discussion): self
    {
        $this->discussion = $discussion;
        return $this;
    }

    public function getMusic(): ?MusicPreference
    {
        return $this->music;
    }

    public function setMusic(MusicPreference $music): self
    {
        $this->music = $music;
        return $this;
    }

    public function getSmoking(): ?SmokingPreference
    {
        return $this->smoking;
    }

    public function setSmoking(SmokingPreference $smoking): self
    {
        $this->smoking = $smoking;
        return $this;
    }

    public function getPets(): ?PetPreference
    {
        return $this->pets;
    }

    public function setPets(PetPreference $pets): self
    {
        $this->pets = $pets;
        return $this;
    }
}