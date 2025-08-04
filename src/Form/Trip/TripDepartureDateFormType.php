<?php

namespace App\Form\Trip;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class TripDepartureDateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('departureDate', DateType::class, [
            'label' => false,
            'widget' => 'single_text',
            'html5' => true,
            'constraints' => [
                new GreaterThanOrEqual('today', message: 'La date de départ ne peut pas être dans le passé.'),
            ],
        ]);
    }
}