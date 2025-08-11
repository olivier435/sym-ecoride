<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ZipCodeValidator\Constraints\ZipCode;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe un compte avec cet email')]
#[UniqueEntity(fields: ['pseudo'], message: 'Il existe un compte avec ce pseudo')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(
        message: 'L\'adresse e-mail {{ value }} est incorrecte',
    )]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $authCode;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Votre prénom doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Votre prénom ne peut excéder {{ limit }} caractères',
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        minMessage: 'Votre nom doit comporter au moins {{ limit }} caractères',
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Merci d\'indiquer votre adresse'
    )]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    #[ZipCode([
        'iso' => 'FR',
        'message' => 'Le code postal n\'est pas valide'
    ])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Merci d\'indiquer votre ville'
    )]
    private ?string $city = null;

    #[ORM\Column(type: 'phone_number')]
    #[AssertPhoneNumber(defaultRegion: 'FR')]
    private ?PhoneNumber $phone = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Avatar $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdTokenAt = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(
        message: 'Merci d\'indiquer votre pseudo'
    )]
    private ?string $pseudo = null;

    /**
     * @var Collection<int, Car>
     */
    #[ORM\OneToMany(targetEntity: Car::class, mappedBy: 'user')]
    private Collection $cars;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'driver')]
    private Collection $tripsAsDriver;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: TravelPreference::class, cascade: ['persist', 'remove'])]
    private ?TravelPreference $travelPreference = null;

    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: "Le crédit ne peut pas être négatif."
    )]
    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $credit = 0;

    /**
     * @var Collection<int, TripPassenger>
     */
    #[ORM\OneToMany(targetEntity: TripPassenger::class, mappedBy: 'user')]
    private Collection $tripPassengers;

    /**
     * @var Collection<int, Testimonial>
     */
    #[ORM\OneToMany(targetEntity: Testimonial::class, mappedBy: 'author')]
    private Collection $testimonials;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->tripsAsDriver = new ArrayCollection();
        $this->tripPassengers = new ArrayCollection();
        $this->testimonials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(?PhoneNumber $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): static
    {
        // unset the owning side of the relation if necessary
        if ($avatar === null && $this->avatar !== null) {
            $this->avatar->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($avatar !== null && $avatar->getUser() !== $this) {
            $avatar->setUser($this);
        }

        $this->avatar = $avatar;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getCreatedTokenAt(): ?\DateTimeImmutable
    {
        return $this->createdTokenAt;
    }

    public function setCreatedTokenAt(?\DateTimeImmutable $createdTokenAt): static
    {
        $this->createdTokenAt = $createdTokenAt;

        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setUser($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getUser() === $this) {
                $car->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTripsAsDriver(): Collection
    {
        return $this->tripsAsDriver;
    }

    public function addTripsAsDriver(Trip $tripsAsDriver): static
    {
        if (!$this->tripsAsDriver->contains($tripsAsDriver)) {
            $this->tripsAsDriver->add($tripsAsDriver);
            $tripsAsDriver->setDriver($this);
        }

        return $this;
    }

    public function removeTripsAsDriver(Trip $tripsAsDriver): static
    {
        if ($this->tripsAsDriver->removeElement($tripsAsDriver)) {
            // set the owning side to null (unless already changed)
            if ($tripsAsDriver->getDriver() === $this) {
                $tripsAsDriver->setDriver(null);
            }
        }

        return $this;
    }

    public function getTravelPreference(): ?TravelPreference
    {
        return $this->travelPreference;
    }

    public function setTravelPreference(TravelPreference $travelPreference): static
    {
        // set the owning side of the relation if necessary
        if ($travelPreference->getUser() !== $this) {
            $travelPreference->setUser($this);
        }

        $this->travelPreference = $travelPreference;

        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * @return Collection<int, TripPassenger>
     */
    public function getTripPassengers(): Collection
    {
        return $this->tripPassengers;
    }

    public function addTripPassenger(TripPassenger $tripPassenger): static
    {
        if (!$this->tripPassengers->contains($tripPassenger)) {
            $this->tripPassengers->add($tripPassenger);
            $tripPassenger->setUser($this);
        }

        return $this;
    }

    public function removeTripPassenger(TripPassenger $tripPassenger): static
    {
        if ($this->tripPassengers->removeElement($tripPassenger)) {
            // set the owning side to null (unless already changed)
            if ($tripPassenger->getUser() === $this) {
                $tripPassenger->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Testimonial>
     */
    public function getTestimonials(): Collection
    {
        return $this->testimonials;
    }

    public function addTestimonial(Testimonial $testimonial): static
    {
        if (!$this->testimonials->contains($testimonial)) {
            $this->testimonials->add($testimonial);
            $testimonial->setAuthor($this);
        }

        return $this;
    }

    public function removeTestimonial(Testimonial $testimonial): static
    {
        if ($this->testimonials->removeElement($testimonial)) {
            // set the owning side to null (unless already changed)
            if ($testimonial->getAuthor() === $this) {
                $testimonial->setAuthor(null);
            }
        }

        return $this;
    }
}