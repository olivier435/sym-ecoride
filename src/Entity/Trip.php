<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    public const STATUS_UPCOMING = 'à venir';
    public const STATUS_ONGOING = 'en cours';
    public const STATUS_COMPLETED = 'effectué';
    public const STATUS_CANCELLED = 'annulé';
    public const STATUS_VALIDATED = 'validé';
    public const STATUS_REPORTED = 'signalé';

    public const STATUSES = [
        self::STATUS_UPCOMING,
        self::STATUS_ONGOING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_VALIDATED,
        self::STATUS_REPORTED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\GreaterThanOrEqual('today', message: 'La date de départ ne peut pas être dans le passé.')]
    private ?\DateTimeImmutable $departureDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $arrivalDate = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeInterface $departureTime = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeInterface $arrivalTime = null;

    #[ORM\Column(length: 255)]
    #[CustomAssert\ValidAddress()]
    private ?string $departureAddress = null;

    #[ORM\Column(length: 255)]
    #[CustomAssert\ValidAddress()]
    private ?string $arrivalAddress = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: Trip::STATUSES, message: 'Statut invalide.')]
    private ?string $status = self::STATUS_UPCOMING;

    #[ORM\Column]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThanOrEqual(4)]
    private ?int $seatsAvailable = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero()]
    private ?int $pricePerPerson = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[ORM\ManyToOne(inversedBy: 'tripsAsDriver')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $driver = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tripsAsPassenger')]
    #[ORM\JoinTable(name: 'trip_passengers')]
    private Collection $passengers;

    #[ORM\ManyToOne(inversedBy: 'departureTrips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $departureCity = null;

    #[ORM\ManyToOne(inversedBy: 'arrivalTrips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $arrivalCity = null;

    public function __construct()
    {
        $this->passengers = new ArrayCollection();
        $this->status = self::STATUS_UPCOMING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartureDate(): ?\DateTimeImmutable
    {
        return $this->departureDate;
    }

    public function setDepartureDate(\DateTimeImmutable $departureDate): static
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeImmutable
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(\DateTimeImmutable $arrivalDate): static
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeInterface
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(\DateTimeInterface $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTimeInterface $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getDepartureAddress(): ?string
    {
        return $this->departureAddress;
    }

    public function setDepartureAddress(string $departureAddress): static
    {
        $this->departureAddress = $departureAddress;

        return $this;
    }

    public function getArrivalAddress(): ?string
    {
        return $this->arrivalAddress;
    }

    public function setArrivalAddress(string $arrivalAddress): static
    {
        $this->arrivalAddress = $arrivalAddress;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSeatsAvailable(): ?int
    {
        return $this->seatsAvailable;
    }

    public function setSeatsAvailable(int $seatsAvailable): static
    {
        $this->seatsAvailable = $seatsAvailable;

        return $this;
    }

    public function getPricePerPerson(): ?int
    {
        return $this->pricePerPerson;
    }

    public function setPricePerPerson(int $pricePerPerson): static
    {
        $this->pricePerPerson = $pricePerPerson;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPassengers(): Collection
    {
        return $this->passengers;
    }

    public function addPassenger(User $passenger): static
    {
        if (!$this->passengers->contains($passenger)) {
            $this->passengers->add($passenger);
            if (!$passenger->getTripsAsPassenger()->contains($this)) {
                $passenger->addTripsAsPassenger($this);
            }
        }

        return $this;
    }

    public function removePassenger(User $passenger): static
    {
        if ($this->passengers->removeElement($passenger)) {
            if ($passenger->getTripsAsPassenger()->contains($this)) {
                $passenger->removeTripsAsPassenger($this);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s à %s (%s)', $this->departureAddress, $this->arrivalAddress, $this->departureDate?->format('d/m/Y'));
    }

    public function getDepartureCity(): ?City
    {
        return $this->departureCity;
    }

    public function setDepartureCity(?City $departureCity): static
    {
        $this->departureCity = $departureCity;

        return $this;
    }

    public function getArrivalCity(): ?City
    {
        return $this->arrivalCity;
    }

    public function setArrivalCity(?City $arrivalCity): static
    {
        $this->arrivalCity = $arrivalCity;

        return $this;
    }

    public function getSlugSource(): string
    {
        return sprintf(
            '%s-%s-%s',
            $this->getDepartureCity()?->getName() ?? '',
            $this->getArrivalCity()?->getName() ?? '',
            $this->getDepartureDate()?->format('d-m-Y') ?? ''
        );
    }

    public function isFull(): bool
    {
        return $this->getPassengers()->count() >= $this->getSeatsAvailable();
    }

    public function getSeatsLeft(): int
    {
        return max(0, $this->getSeatsAvailable() - $this->getPassengers()->count());
    }

    public function isCancellable(): bool
    {
        return $this->getStatus() === self::STATUS_UPCOMING;
    }

    public function isEditable(): bool
    {
        // Edit si statut upcoming, aucun passager, ET date future OU statut cancelled
        return (
            ($this->getStatus() === self::STATUS_UPCOMING && $this->getPassengers()->isEmpty() && $this->getDepartureDate() >= new \DateTimeImmutable('today'))
            || $this->getStatus() === self::STATUS_CANCELLED
        );
    }

    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_UPCOMING;
    }

    public function canBeStarted(): bool
    {
        if ($this->status !== self::STATUS_UPCOMING) {
            return false;
        }

        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);

        if (!$this->departureDate || !$this->departureTime) {
            return false;
        }

        // Recompose une DateTimeImmutable en Europe/Paris à partir des morceaux
        $date = $this->departureDate->format('Y-m-d');
        $time = $this->departureTime->format('H:i:s');
        $departureDateTime = new \DateTimeImmutable("$date $time", $tz);

        $earliest = $departureDateTime->modify('-10 minutes');
        $latest = $departureDateTime->modify('+30 minutes');

        if ($now < $earliest || $now > $latest) {
            return false;
        }
        return true;
    }

    public function canBeCompleted(): bool
    {
        if ($this->status !== self::STATUS_ONGOING) {
            return false;
        }

        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);

        if (!$this->arrivalDate || !$this->arrivalTime) {
            return false;
        }

        $date = $this->arrivalDate->format('Y-m-d');
        $time = $this->arrivalTime->format('H:i:s');
        $arrivalDateTime = new \DateTimeImmutable("$date $time", $tz);

        // On autorise le passage à "effectué" de 30 minutes avant l'arrivée à 2h après
        $earliest = $arrivalDateTime->modify('-30 minutes');
        $latest = $arrivalDateTime->modify('+2 hours');

        if ($now < $earliest || $now > $latest) {
            return false;
        }

        return true;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isReported(): bool
    {
        return $this->status === self::STATUS_REPORTED;
    }

    public function isExpiredWithoutPassenger(): bool
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);

        $date = $this->departureDate?->format('Y-m-d');
        $time = $this->departureTime?->format('H:i:s');
        $departureDateTime = new \DateTimeImmutable("$date $time", $tz);

        return $this->status === self::STATUS_UPCOMING
            && $this->getPassengers()->isEmpty()
            && $departureDateTime < $now;
    }
}