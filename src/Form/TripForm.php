<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Trip;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripForm extends AbstractType
{
    public function __construct(protected Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $builder
            ->add('departureAddress', TextType::class, [
                'label' => 'Adresse de départ',
                'attr' => ['placeholder' => 'Saisir l\'adresse de départ']
            ])
            ->add('arrivalAddress', TextType::class, [
                'label' => 'Adresse d\'arrivée',
                'attr' => ['placeholder' => 'Saisir l\'adresse d\'arrivée']
            ])
            ->add('departureDate', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text'
            ])
            ->add('departureTime', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'with_seconds' => false,
            ])
            ->add('arrivalDate', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text'
            ])
            ->add('arrivalTime', TimeType::class, [
                'label' => 'Heure d\'arrivée',
                'widget' => 'single_text',
                'with_seconds' => false,
            ])
            ->add('seatsAvailable', ChoiceType::class, [
                'label' => 'Nombre de places disponibles',
                'choices' => [
                    '1 place' => 1,
                    '2 places' => 2,
                    '3 places' => 3,
                    '4 places' => 4,
                ]
            ])
            ->add('pricePerPerson', MoneyType::class, [
                'label' => 'Prix par personne',
                'currency' => 'EUR',
                'divisor' => 100, // conversion centimes <=> euros
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut du voyage',
                'choices' => array_combine(Trip::STATUSES, Trip::STATUSES),
                'data' => Trip::STATUS_UPCOMING
            ])
            ->add('car', EntityType::class, [
                'label' => 'Véhicule utilisé',
                'class' => Car::class,
                'choices' => $user ? $user->getCars() : [],
                'choice_label' => function (Car $car) {
                    return (string) $car;
                },
                'placeholder' => 'Sélectionner un véhicule',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}