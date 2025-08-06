<?php

namespace App\Form\TravelPreference;

use App\Enum\DiscussionPreference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TravelPreferenceDiscussionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('discussion', ChoiceType::class, [
            'label' => 'Discussion',
            'choices' => array_combine(
                array_map(fn($e) => $e->label(), DiscussionPreference::cases()),
                DiscussionPreference::cases()
            ),
            'choice_attr' => function ($choice, $key, $value) {
                /** @var DiscussionPreference $choice */
                // return [
                //     'data-svg' => $choice->svg(),
                // ];
                return [
                    'data-icon' => $choice->icon()
                ];
            },
            'choice_value' => fn(?DiscussionPreference $enum) => $enum?->value,
            'expanded' => true, // boutons radio
            'multiple' => false,
            'required' => true,
        ]);
    }
}