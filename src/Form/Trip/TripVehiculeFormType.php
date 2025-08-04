<?php

namespace App\Form\Trip;

use App\Entity\Car;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripVehiculeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        $choices = $user?->getCars()?->toArray() ?? [];

        usort($choices, function (Car $a, Car $b) {
            $labelA = $a->getBrand()?->getName() . ' ' . $a->getModel()?->getName();
            $labelB = $b->getBrand()?->getName() . ' ' . $b->getModel()?->getName();
            return strcmp($labelA, $labelB);
        });

        $builder->add('car', EntityType::class, [
            'class' => Car::class,
            'choices' => $choices,
            'choice_label' => fn(Car $car) => sprintf('%s %s (%s)', $car->getBrand()?->getName(), $car->getModel()?->getName(), $car->getColor()),
            'placeholder' => 'Choisissez un véhicule',
            'label' => 'Votre véhicule',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }
}