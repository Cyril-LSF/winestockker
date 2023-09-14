<?php

namespace App\Form\Subscription;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Nom. Ex: 7 jours",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un nom",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le nom doit contenir minimum {{ limit }} caractères",
                        'max' => 255,
                        'maxMessage' => "Le nom doit contenir maximum {{ limit }} caractères"
                    ]),
                    new Regex([
                        'pattern' => '/^[\w\s\-àáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]{3,255}$/i',
                        'match' => true,
                        'message' => "Le nom ne peut contenir que des lettres, des chiffres, des tirets et des espaces. Caractères min.3 - max.255",
                    ]),
                ],
            ])
            ->add('price', TextType::class, [
                'label' => "Prix",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Prix. Ex: 2",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un prix",
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => "Le prix doit contenir minimum {{ limit }} caractères",
                        'max' => 50,
                        'maxMessage' => "Le prix doit contenir maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d{1,50}$/',
                        'match' => true,
                        'message' => "Le prix ne doit contenir que des chiffres, caractères min.1 - max.50",
                    ])
                ]
            ])
            ->add('priceInCents', TextType::class, [
                'label' => "Prix en centime",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Prix en centime. Ex: pour 2€ -> 200",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un prix en centime",
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => "Le prix en centime doit contenir minimum {{ limit }} caractères",
                        'max' => 50,
                        'maxMessage' => "Le prix en centime doit contenir maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d{1,50}$/',
                        'match' => true,
                        'message' => "Le prix en centime ne doit contenir que des chiffres, caractères min.1 - max.50",
                    ]),
                ]
            ])
            ->add('duration', NumberType::class, [
                'label' => "Durée en jour",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Durée. Ex: 7",
                ],
                'invalid_message' => "La durée ne doit contenir que des chiffres",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une durée",
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'match' => true,
                        'message' => "La durée ne doit contenir que des chiffres",
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
