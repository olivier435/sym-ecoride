<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['user'];

        // Définition des statuts disponibles selon le contexte
        $statusChoices = [Trip::STATUS_UPCOMING => Trip::STATUS_UPCOMING];
        if ($options['allow_status_choice'] ?? false) {
            $statusChoices = [
                Trip::STATUS_UPCOMING => Trip::STATUS_UPCOMING,
                Trip::STATUS_CANCELLED => Trip::STATUS_CANCELLED,
            ];
        }

        $builder
            ->add('departureAddress', TextType::class, [
                'label' => 'Adresse de départ',
                'attr' => [
                    'data-address-formatter-target' => 'input',
                    'autocomplete' => 'off',
                    'placeholder' => '12 rue Victor Hugo, 75001 Paris'
                ]
            ])
            ->add('arrivalAddress', TextType::class, [
                'label' => 'Adresse d\'arrivée',
                'attr' => [
                    'data-address-formatter-target' => 'input',
                    'autocomplete' => 'off',
                    'placeholder' => '12 rue Victor Hugo, 75001 Paris'
                ]
            ])
            ->add('departureDate', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('departureTime', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'with_seconds' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('arrivalDate', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('arrivalTime', TimeType::class, [
                'label' => 'Heure d\'arrivée',
                'widget' => 'single_text',
                'with_seconds' => false,
                'input' => 'datetime_immutable',
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
            // ->add('pricePerPerson', MoneyType::class, [
            //     'label' => 'Prix par personne',
            //     'currency' => 'EUR',
            //     'divisor' => 100,
            // ])
            ->add('pricePerPerson', IntegerType::class, [
                'label' => 'Prix par personne (crédits)',
                'attr' => [
                    'min' => 2,
                    'class' => 'text-center'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut du voyage',
                'choices' => $statusChoices,
                'data' => $builder->getData()->getStatus() ?? Trip::STATUS_UPCOMING,
            ])
            ->add('car', EntityType::class, [
                'label' => 'Véhicule utilisé',
                'class' => Car::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('c')
                        ->where('c.user = :user')
                        ->setParameter('user', $user);
                },
                'choice_label' => fn(Car $car) => (string) $car,
                'placeholder' => 'Sélectionner un véhicule',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
            'user' => null, // option personnalisée
            'allow_status_choice' => false,
        ]);
    }
}