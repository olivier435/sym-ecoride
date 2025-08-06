<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'departureCity')]
    private Collection $departureTrips;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'arrivalCity')]
    private Collection $arrivalTrips;

    public function __construct()
    {
        $this->departureTrips = new ArrayCollection();
        $this->arrivalTrips = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getDepartureTrips(): Collection
    {
        return $this->departureTrips;
    }

    public function addDepartureTrip(Trip $departureTrip): static
    {
        if (!$this->departureTrips->contains($departureTrip)) {
            $this->departureTrips->add($departureTrip);
            $departureTrip->setDepartureCity($this);
        }

        return $this;
    }

    public function removeDepartureTrip(Trip $departureTrip): static
    {
        if ($this->departureTrips->removeElement($departureTrip)) {
            // set the owning side to null (unless already changed)
            if ($departureTrip->getDepartureCity() === $this) {
                $departureTrip->setDepartureCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getArrivalTrips(): Collection
    {
        return $this->arrivalTrips;
    }

    public function addArrivalTrip(Trip $arrivalTrip): static
    {
        if (!$this->arrivalTrips->contains($arrivalTrip)) {
            $this->arrivalTrips->add($arrivalTrip);
            $arrivalTrip->setArrivalCity($this);
        }

        return $this;
    }

    public function removeArrivalTrip(Trip $arrivalTrip): static
    {
        if ($this->arrivalTrips->removeElement($arrivalTrip)) {
            // set the owning side to null (unless already changed)
            if ($arrivalTrip->getArrivalCity() === $this) {
                $arrivalTrip->setArrivalCity(null);
            }
        }

        return $this;
    }
}