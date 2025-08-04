<?php

namespace App\Form\Trip;

use App\Validator\ValidAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TripDepartureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departureAddress', TextType::class, [
                'label' => false,
                'attr' => [
                    'data-address-formatter-target' => 'input',
                    'autocomplete' => 'off',
                    'placeholder' => '12 rue Victor Hugo, 75001 Paris'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse de départ']),
                    new ValidAddress(),
                ]
            ]);
    }
}