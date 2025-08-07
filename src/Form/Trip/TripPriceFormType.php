<?php

namespace App\Form\Trip;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TripPriceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // CHAMP A DECOMMENTER SI PRIX EN EUROS
        // $builder->add('pricePerPerson', MoneyType::class, [
        //     'label' => 'Prix par personne (€)',
        //     'currency' => 'EUR',
        //     'divisor' => 100,
        //     'attr' => [
        //         'data-price-target' => 'input',
        //         'min' => 0,
        //         'class' => 'text-center',
        //     ],
        //     'constraints' => [
        //         new Assert\NotBlank(['message' => 'Veuillez indiquer un prix']),
        //         new Assert\GreaterThan(['value' => 0, 'message' => 'Le prix doit être supérieur à zéro']),
        //     ],
        // ]);
        $builder->add('pricePerPerson', IntegerType::class, [
            'label' => 'Prix par personne (crédits)',
            'attr' => [
                'data-price-target' => 'input',
                'min' => 2,
                'class' => 'text-center',
            ],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Veuillez indiquer un prix']),
                new Assert\GreaterThanOrEqual(['value' => 2, 'message' => 'Le prix doit être au moins de 2 crédits']),
            ],
        ]);
    }
}