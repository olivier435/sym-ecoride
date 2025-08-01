<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['registration'],
    message: 'Une voiture avec cette immatriculation existe déjà'
)]
#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    public const ENERGY_ELECTRIC = 'Electrique';
    public const ENERGY_ESSENCE = 'Essence';
    public const ENERGY_DIESEL = 'Diesel';
    public const ENERGY_HYBRID = 'Hybride';
    public const ENERGY_E85 = 'E85 (Bioéthanol)';
    public const ENERGY_GAZ = 'Gaz Naturel (GNV) et GPL';

    public const ENERGIES = [
        self::ENERGY_ELECTRIC,
        self::ENERGY_ESSENCE,
        self::ENERGY_DIESEL,
        self::ENERGY_HYBRID,
        self::ENERGY_E85,
        self::ENERGY_GAZ
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type d\'énergie est obligatoire.')]
    #[Assert\Choice(
        choices: self::ENERGIES,
        message: 'Le type d\'énergie sélectionné est invalide'
    )]
    private ?string $energy = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'L\'immatriculation est obligatoire.')]
    #[Assert\Length(
        max: 10,
        maxMessage: 'L\'immatriculation ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/',
        message: 'L\'immatriculation doit être au format AA-123-AA.'
    )]
    private ?string $registration = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La couleur est obligatoire.')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'La couleur ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $color = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date de première immatriculation est obligatoire.')]
    #[Assert\LessThanOrEqual('today', message: 'La date ne peut pas être dans le futur.')]
    private ?\DateTimeImmutable $firstregistrationAt = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un utilisateur doit être associé à la voiture.')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La marque est obligatoire.')]
    private ?Brand $brand = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le modèle est obligatoire.')]
    private ?Model $model = null;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'car')]
    private Collection $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnergy(): ?string
    {
        return $this->energy;
    }

    public function setEnergy(string $energy): static
    {
        $this->energy = $energy;

        return $this;
    }

    public function getRegistration(): ?string
    {
        return $this->registration;
    }

    public function setRegistration(string $registration): static
    {
        $this->registration = $registration;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getFirstregistrationAt(): ?\DateTimeImmutable
    {
        return $this->firstregistrationAt;
    }

    public function setFirstregistrationAt(\DateTimeImmutable $firstregistrationAt): static
    {
        $this->firstregistrationAt = $firstregistrationAt;

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

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function __toString(): string
    {
        $brandName = $this->brand?->getName() ?? '';
        $modelName = $this->model?->getName() ?? '';
        if ($brandName === '' && $modelName === '') {
            return '';
        }
        // Si l'un des deux est vide, on renvoie l'autre seul, sinon on concatène
        if ($brandName === '') {
            return $modelName;
        }
        if ($modelName === '') {
            return $brandName;
        }

        return sprintf('%s %s', $brandName, $modelName);
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setCar($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getCar() === $this) {
                $trip->setCar(null);
            }
        }

        return $this;
    }
}
