<?php

namespace App\Entity;

use App\Enum\ComplaintType;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ComplaintRepository::class)]
class Complaint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'complaint', targetEntity: TripPassenger::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?TripPassenger $tripPassenger = null;

    #[ORM\Column(type: "string", enumType: ComplaintType::class)]
    #[Assert\NotNull]
    private ?ComplaintType $type = null;

    #[ORM\Column(length: 150)]
    #[Assert\Length(max: 150)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTripPassenger(): ?TripPassenger
    {
        return $this->tripPassenger;
    }

    public function setTripPassenger(?TripPassenger $tripPassenger): static
    {
        $this->tripPassenger = $tripPassenger;
        if ($tripPassenger && $tripPassenger->getComplaint() !== $this) {
            $tripPassenger->setComplaint($this);
        }
        return $this;
    }

    public function getType(): ?ComplaintType
    {
        return $this->type;
    }

    public function setType(ComplaintType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}