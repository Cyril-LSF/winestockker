<?php

namespace App\Form;

use App\Entity\User;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un prénom",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le prénom doit comporter au minimum {{ limit }} caractères",
                        'max' => 50,
                        'maxMessage' => "Le prénom doit comporter au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^([a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+)([-\s]?)([a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+)$/',
                        'match' => true,
                        'message' => "Seul les lettres, les tirets et les espaces sont autorisés",
                    ])
                ],
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
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un prénom",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le prénom doit comporter au minimum {{ limit }} caractères",
                        'max' => 50,
                        'maxMessage' => "Le prénom doit comporter au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^([a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+)([-\s]?)([a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]+)$/',
                        'match' => true,
                        'message' => "Seul les lettres, les tirets et les espaces sont autorisés",
                    ])
                ],
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
                'data' => new DateTime(),
                'required' => true,
                'constraints' => [
                    new NotNull([
                        'message' => "Veuillez saisir une date de naissance",
                    ]),
                    new Callback([
                        'callback' => static function (DateTime $data, ExecutionContextInterface $context) {
                            $now = date_create();
                            $interval = date_diff($data, $now);
                            if ($interval->y < 18) {
                                $context->buildViolation("Vous devait avoir 18 ans minimum pour vous inscrire")
                                ->addViolation();
                            }
                        }
                    ])
                ]
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
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une adresse email",
                    ]),
                    new Regex([
                        'pattern' => '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/',
                        'match' => true,
                        'message' => "Veuillez saisir une adresse email valide",
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => "Les mots de passe ne sont pas identique",
                'first_options' => [
                    'label' => "Mot de passe",
                    'label_attr' => [
                        'class' => "form-label",
                    ],
                    'attr' => [
                        'class' => "form-control",
                        'placeholder' => "Mot de passe",
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez saisir un mot de passe",
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'match' => true,
                            'message' => "Votre mot de passe doit contenir au minimum 8 cractères dont 1 minuscule, 1 majuscule, 1 chiffre et 1 caractères spécial",
                        ])
                    ]
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
                        'message' => "Vous devez accepter les conditions d'utilisation",
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
