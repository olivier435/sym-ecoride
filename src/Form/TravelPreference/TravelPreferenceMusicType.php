<?php

namespace App\Form\TravelPreference;

use App\Enum\MusicPreference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TravelPreferenceMusicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('music', ChoiceType::class, [
            'label' => 'Musique',
            'choices' => array_combine(
                array_map(fn($e) => $e->label(), MusicPreference::cases()),
                MusicPreference::cases()
            ),
            'choice_attr' => function ($choice, $key, $value) {
                /** @var MusicPreference $choice */
                return [
                    'data-icon' => $choice->icon()
                ];
            },
            'choice_value' => fn(?MusicPreference $enum) => $enum?->value,
            'expanded' => true,
            'multiple' => false,
            'required' => true,
        ]);
    }
}