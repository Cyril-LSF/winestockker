<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
                'attr' => [
                    'placeholder' => "Prénom",
                    'class' => "form-control",
                ],
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom",
                'attr' => [
                    'placeholder' => "Nom",
                    'class' => "form-control",
                ],
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => "Date de naissance",
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Date de naissance",
                ],
                'label_attr' => [
                    'class' => "form-label",
                ],
                'widget' => "single_text",
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => "Email",
                'attr' => [
                    'placeholder' => "Email",
                    'class' => "form-control",
                ],
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
            ])
            // ->add('plainPassword', PasswordType::class, [
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'mapped' => false,
            //     'attr' => ['autocomplete' => 'new-password'],
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Please enter a password',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Your password should be at least {{ limit }} characters',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //         ]),
            //     ],
            // ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => "Mot de passe",
                    'label_attr' => [
                        'class' => "form-label",
                    ],
                    'attr' => [
                        'class' => "form-control",
                        'placeholder' => "Mot de passe",
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
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'attr' => [
                    'class' => "form-check-input me-2",
                ],
                'required' => true,
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
