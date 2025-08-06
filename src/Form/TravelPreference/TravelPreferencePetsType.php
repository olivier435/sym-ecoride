<?php

namespace App\Form\TravelPreference;

use App\Enum\PetPreference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TravelPreferencePetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pets', ChoiceType::class, [
            'label' => 'Votre préférence animaux',
            'choices' => array_combine(
                array_map(fn($e) => $e->label(), PetPreference::cases()),
                PetPreference::cases()
            ),
            'choice_attr' => function ($choice, $key, $value) {
                /** @var PetPreference $choice */
                return [
                    'data-icon' => $choice->icon()
                ];
            },
            'choice_value' => fn(?PetPreference $enum) => $enum?->value,
            'expanded' => true,
            'multiple' => false,
            'required' => true,
        ]);
    }
}