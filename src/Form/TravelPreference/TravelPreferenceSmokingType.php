<?php

namespace App\Form\TravelPreference;

use App\Enum\SmokingPreference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TravelPreferenceSmokingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('smoking', ChoiceType::class, [
            'label' => 'Votre préférence tabac',
            'choices' => array_combine(
                array_map(fn($e) => $e->label(), SmokingPreference::cases()),
                SmokingPreference::cases()
            ),
            'choice_attr' => function ($choice, $key, $value) {
                /** @var SmokingPreference $choice */
                return [
                    'data-icon' => $choice->icon()
                ];
            },
            'choice_value' => fn(?SmokingPreference $enum) => $enum?->value,
            'expanded' => true,
            'multiple' => false,
            'required' => true,
        ]);
    }
}