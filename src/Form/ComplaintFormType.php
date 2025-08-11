<?php

namespace App\Form;

use App\Entity\Complaint;
use App\Enum\ComplaintType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplaintFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Motif',
                'choices' => array_combine(
                    array_map(fn(ComplaintType $ct) => $ct->label(), ComplaintType::cases()),
                    ComplaintType::cases()
                ),
                'choice_value' => fn(?ComplaintType $enum) => $enum?->value,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Détaillez votre problème',
                'attr' => [
                    'maxlength' => 150,
                    'placeholder' => '150 caractères maximum…',
                    'class' => 'wyelsy',
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Complaint::class,
        ]);
    }
}