<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => "Les mots de passe ne sont pas identique",
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'label' => "Nouveau mot de passe",
                    'label_attr' => [
                        'class' => "form-label",
                    ],
                    'attr' => [
                        'class' => "form-control",
                        'placeholder' => "Mot de passe",
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'match' => true,
                            'message' => "Votre mot de passe doit contenir au minimum 8 cractères dont 1 minuscule, 1 majuscule, 1 chiffre et 1 caractères spécial",
                        ])
                    ],
                ],
                'second_options' => [
                    'label' => "Confirmation du mot de passe",
                    'label_attr' => [
                        'class' => "form-label",
                    ],
                    'attr' => [
                        'class' => "form-control",
                        'placeholder' => "Confirmation du mot de passe",
                    ],
                ],
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
