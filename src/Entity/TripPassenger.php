<?php

namespace App\Entity;

use App\Repository\TripPassengerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripPassengerRepository::class)]
class TripPassenger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Trip::class, inversedBy: 'tripPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tripPassengers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 20, options: ['default' => 'pending'])]
    private ?string $validationStatus = 'pending'; // 'pending', 'validated', 'reported'

    #[ORM\OneToOne(mappedBy: 'tripPassenger', targetEntity: Complaint::class, cascade: ['persist', 'remove'])]
    private ?Complaint $complaint = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getValidationStatus(): ?string
    {
        return $this->validationStatus;
    }

    public function setValidationStatus(string $validationStatus): static
    {
        $this->validationStatus = $validationStatus;

        return $this;
    }

    public function getComplaint(): ?Complaint
    {
        return $this->complaint;
    }

    public function setComplaint(?Complaint $complaint): static
    {
        if ($this->complaint && $this->complaint !== $complaint) {
            $this->complaint->setTripPassenger(null);
        }
        $this->complaint = $complaint;
        if ($complaint && $complaint->getTripPassenger() !== $this) {
            $complaint->setTripPassenger($this);
        }
        return $this;
    }

    public function __toString(): string
    {
        // Exemple : pseudo du passager + id du trajet pour que ce soit lisible
        $user = $this->getUser();
        $trip = $this->getTrip();
        return ($user ? $user->getFullName() : 'Passager')
            . ' - Trajet n°' . ($trip ? $trip->getId() : 'N/A');
    }
}