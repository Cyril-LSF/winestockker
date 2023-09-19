<?php

namespace App\Form\Address;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de l'adresse *",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: Domicile",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un nom",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le nom doit contenir au minimum {{ limit }} caractères",
                        'max' => 255,
                        'maxMessage' => "Le nom doit contenir au maximum {{ limit }} caractères",
                    ])
                ]
            ])
            ->add('streetNumber', TextType::class, [
                'label' => "N° *",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: 12",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un numéro",
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => "Le numéro doit contenir au minimum {{ limit }} caractères",
                        'max' => 10,
                        'maxMessage' => "Le numéro doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => "/^\d+$/",
                        'match' => true,
                        'message' => "Le numéro ne doit contenir que des chiffres",
                    ])
                ]
            ])
            ->add('streetNumberExtension', TextType::class, [
                'label' => "Extension",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: Bis",
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z]*$/',
                        'match' => true,
                        'message' => "L'extension ne doit contenir que des lettres",
                    ])
                ]
            ])
            ->add('streetType', ChoiceType::class, [
                'label' => "type *",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: Rue",
                ],
                'choices' => [
                    'Rue' => "rue",
                    'Impasse' => "impasse",
                    'Quai' => "quai",
                    'Boulevard' => "boulevard",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez sélectionner un type",
                    ]),
                ],
            ])
            ->add('streetName', TextType::class, [
                'label' => "Nom de rue *",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: de paris",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un nom de rue",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le nom de rue doit contenir au minimum {{ limit }} caractères",
                        'max' => 100,
                        'maxMessage' => "Le nom de rue doit contenir au maximum {{ limit }} caractères",
                    ]),
                ],
            ])
            ->add('complement', TextType::class, [
                'label' => "Complément d'adresse",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: 3e étage, appartement 345",
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Le complément d'adresse doit contenir au maximum {{ limit }} caractères",
                    ]),
                ],
            ])
            ->add('postalcode', TextType::class, [
                'label' => "Code postal *",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: 59130",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un code postal",
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => "Le code postal doit contenir au minimum {{ limit }} caractères",
                        'max' => 10,
                        'maxMessage' => "Le code postal doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => "/^\d+$/",
                        'match' => true,
                        'message' => "Le code postal ne doit contenir que des chiffres",
                    ]),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => "Ville *",
                'label_attr' => [
                    'class' => "form-label text-muted",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control form-control-lg",
                    'placeholder' => "Ex: Paris",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une ville",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "La ville doit contenir au minimum {{ limit }} caractères",
                        'max' => 200,
                        'maxMessage' => "La ville doit contenir au maximum {{ limit }} cractères",
                    ]),
                    new Regex([
                        'pattern' => "/^[a-zéèêëàâîïôöûü]([-']?[a-zéèêëàâîïôöûü])*$/i",
                        'match' => true,
                        'message' => "La ville ne peut contenir que des lettres et des tirets",
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
