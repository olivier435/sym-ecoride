<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureCity', CityAutocompleteField::class, [
                'label' => 'Ville de départ',
                'attr' => [
                    'placeholder' => 'Ex: Paris',
                    'autocomplete' => 'off',
                ],
                'extra_options' => [
                    'field' => 'departureCity',
                ],
            ])
            ->add('arrivalCity', CityAutocompleteField::class, [
                'label' => 'Ville d\'arrivée',
                'attr' => [
                    'placeholder' => 'Ex: Lyon',
                    'autocomplete' => 'off',
                ],
                'extra_options' => [
                    'field' => 'arrivalCity',
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du voyage',
                'widget' => 'single_text',
                'html5' => true,
                'data' => new \DateTimeImmutable('today'),
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas de data_class, c'est une simple recherche
        ]);
    }
}