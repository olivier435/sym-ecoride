<?php

namespace App\Form\Trip;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TripDepartureTimeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('departureTime', TimeType::class, [
            'label' => false,
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
            'html5' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez indiquer une heure de départ']),
            ],
        ]);
    }
}