<?php

namespace App\Entity;

use App\Enum\ComplaintType;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
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

    #[ORM\Column]
    private ?bool $ticketClosed = false;

    #[ORM\Column]
    private ?bool $ticketResolved = false;

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

    public function isTicketClosed(): ?bool
    {
        return $this->ticketClosed;
    }

    public function setTicketClosed(bool $ticketClosed): static
    {
        $this->ticketClosed = $ticketClosed;

        return $this;
    }

    public function isTicketResolved(): ?bool
    {
        return $this->ticketResolved;
    }

    public function setTicketResolved(bool $ticketResolved): static
    {
        $this->ticketResolved = $ticketResolved;

        return $this;
    }

    public function getContacts(): string
    {
        $trip = $this->getTripPassenger()?->getTrip();
        $passenger = $this->getTripPassenger()?->getUser();
        $driver = $trip?->getDriver();

        $formatPhone = function ($phone) {
            if ($phone instanceof PhoneNumber) {
                return PhoneNumberUtil::getInstance()
                    ->format($phone, PhoneNumberFormat::NATIONAL);
            }
            return '';
        };

        $txt = "Passager :\n";
        if ($passenger) {
            $txt .= $passenger->getFirstname() . ' ' . $passenger->getLastname() . "\n";
            $txt .= 'Email : ' . $passenger->getEmail() . "\n";
            $txt .= 'Tél : ' . $formatPhone($passenger->getPhone()) . "\n";
        } else {
            $txt .= "inconnu\n";
        }

        $txt .= "\n"; // <-- Ajoute un saut de ligne entre les deux

        $txt .= "Conducteur :\n";
        if ($driver) {
            $txt .= $driver->getFirstname() . ' ' . $driver->getLastname() . "\n";
            $txt .= 'Email : ' . $driver->getEmail() . "\n";
            $txt .= 'Tél : ' . $formatPhone($driver->getPhone()) . "\n";
        } else {
            $txt .= "inconnu\n";
        }

        return $txt;
    }

    public function getStatus(): string
    {
        if ($this->isTicketResolved()) {
            return 'resolved';
        }
        if ($this->isTicketClosed()) {
            return 'closed';
        }
        return 'open';
    }
}