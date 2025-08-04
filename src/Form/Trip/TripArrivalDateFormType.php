<?php

namespace App\Form\Trip;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TripArrivalDateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('arrivalDate', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez indiquer une date d\'arrivée']),
                    new Assert\GreaterThanOrEqual([
                        'value' => (new \DateTime('today'))->format('Y-m-d'),
                        'message' => 'La date ne peut pas être dans le passé.'
                    ])
                ]
            ])
            ->add('arrivalTime', TimeType::class, [
                'label' => 'Heure d\'arrivée',
                'widget' => 'single_text',
                'input' => 'datetime',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez indiquer une heure d\'arrivée']),
                ]
            ]);
    }
}