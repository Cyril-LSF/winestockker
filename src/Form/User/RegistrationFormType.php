<?php

namespace App\Form\User;

use DateTime;
use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom",
                'attr' => [
                    'placeholder' => "Ex: John",
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
                'label' => "Votre nom",
                'attr' => [
                    'placeholder' => "Ex: Doe",
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
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'attr' => [
                    'placeholder' => "Ex: john@doe.fr",
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if (!$user || null === $user->getId()) {
                    $form
                    ->add('birthday', BirthdayType::class, [
                        'label' => "Votre date de naissance",
                        'attr' => [
                            'class' => "form-control",
                            'placeholder' => "Date de naissance",
                        ],
                        'label_attr' => [
                            'class' => "form-label",
                        ],
                        'widget' => "single_text",
                        //'data' => new DateTime(),
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
                                        $context->buildViolation("Vous devez avoir 18 ans minimum pour vous inscrire")
                                        ->addViolation();
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
                            'label' => "Votre mot de passe",
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
                        'label' => "J'accepte les conditions d'utilisation",
                        'label_attr' => [
                            'class' => "form-check-label",
                        ],
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
                    ]);
                } else {
                    $form->add('picture', FileType::class, [
                        'mapped' => false,
                        'required' => false,
                        'label' => "Photo de profil",
                        'label_attr' => [
                            'class' => "form-label",
                        ],
                        'attr' => [
                            'class' => "form-control",
                        ],
                        'constraints' => [
                            new File([
                                'maxSize' => "10000k",
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpg',
                                    'image/jpeg',
                                    'image/svg',
                                ],
                                'mimeTypesMessage' => "Veuillez sélectionner un fichier valide, les extensions autorisées sont: JPG, JPEG, PNG, SVG",
                            ])
                        ]
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
