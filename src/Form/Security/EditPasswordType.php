<?php

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class EditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userPassword = $options['user']->getPassword();
        $builder
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "Ancien mot de passe *",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Mot de passe *",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir votre ancien mot de passe",
                    ]),
                    new Callback([
                        'callback' => static function (?string $data, ExecutionContextInterface $context) use ($userPassword) {
                            if ($data) {
                                $factory = new PasswordHasherFactory([
                                    'common' => ['algorithm' => 'bcrypt'],
                                ]);
                                $passwordHasher = $factory->getPasswordHasher('common');
                                if (!$passwordHasher->verify($userPassword, $data)) {
                                    $context
                                        ->buildViolation("Mot de passe incorrect")
                                        ->addViolation();
                                }
                            }
                        }
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => "Les mots de passe ne sont pas identique",
                'first_options' => [
                    'label' => "Mot de passe *",
                    'label_attr' => [
                        'class' => "form-label",
                    ],
                    'attr' => [
                        'class' => "form-control",
                        'placeholder' => "Mot de passe *",
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez saisir votre nouveau mot de passe",
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'match' => true,
                            'message' => "Votre mot de passe doit contenir au minimum 8 cractères dont 1 minuscule, 1 majuscule, 1 chiffre et 1 caractères spécial",
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmation du mot de passe *",
                    'label_attr' => [
                        'class' => "form-label",
                    ],
                    'attr' => [
                        'class' => "form-control",
                        'placeholder' => "Confirmation du mot de passe *",
                    ],
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null,
        ]);
    }
}
