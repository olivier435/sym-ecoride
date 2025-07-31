<?php

namespace App\Form;

use App\Entity\Car;
use App\Form\DataTransformer\ModelToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarForm extends AbstractType
{
    public function __construct(private readonly ModelToIdTransformer $modelToIdTransformer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = Car::ENERGIES;
        sort($choices);
        $builder
            ->add('energy', ChoiceType::class, [
                'label' => 'Type d\'énergie',
                'placeholder' => 'Choisissez un type d\'énergie',
                'choices' => array_combine($choices, $choices),
            ])
            ->add('registration', TextType::class, [
                'label' => 'Immatriculation',
                'attr' => [
                    'placeholder' => 'AA-123-AA',
                    'pattern' => '^[A-Z]{2}-\d{3}-[A-Z]{2}$',
                    'title' => 'Format attendu : AA-123-AA',
                    'maxlength' => 10,
                    'data-controller' => 'registration-formatter',
                    'data-registration-formatter-target' => 'input',
                    'class' => 'form-control form-control-login',
                ],
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
                'attr' => [
                    'placeholder' => 'ex. Gris Foncé',
                    'class' => 'form-control form-control-login'
                ],
            ])
            ->add('firstregistrationAt', DateType::class, [
                'label' => 'Première immatriculation',
                'widget' => 'single_text',
            ])
            ->add('brand', BrandAutocompleteField::class, [
                'label' => 'Marque',
                'attr' => ['data-model-loader-target' => 'brand'],
            ])
            ->add('model', HiddenType::class, [
                'attr' => ['data-model-loader-target' => 'model'],
            ])
        ;

        $builder->get('model')->addModelTransformer($this->modelToIdTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
