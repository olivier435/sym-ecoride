<?php

namespace App\Form;

use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse email'
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\utilisation',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'toggle' => true,
                'hidden_label' => 'Masquer',
                'visible_label' => 'Afficher',
                'label' => 'Votre mot de passe :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '••••••••'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new PasswordStrength(
                        minScore: PasswordStrength::STRENGTH_STRONG,
                        message: 'Le mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort.'
                    )
                ],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo : *',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre pseudo'
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom'
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom'
                ],
            ])
            ->add('adress', TextType::class, [
                'label' => 'Votre adresse :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Votre code postal :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre code postal'
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre ville :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre ville'
                ],
            ])
            ->add('phone', PhoneNumberType::class, [
                'default_region' => 'FR',
                'format' => PhoneNumberFormat::NATIONAL,
                'label' => 'Votre téléphone :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre téléphone'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
