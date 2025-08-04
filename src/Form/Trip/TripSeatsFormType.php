<?php

namespace App\Form\Trip;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripSeatsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('seatsAvailable', HiddenType::class, [
            'attr' => [
                'data-seats-counter-target' => 'input'
            ],
            'constraints' => [
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Vous devez proposer au moins une place.'
                ]),
                new LessThanOrEqual([
                    'value' => 4,
                    'message' => 'Vous ne pouvez pas proposer plus de 4 places.'
                ]),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // important si on n'utilise pas une entité ici
            'csrf_protection' => true,
        ]);
    }
}