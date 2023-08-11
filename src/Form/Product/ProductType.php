<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProductType extends AbstractType
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
                    'placeholder' => "Ex: 7 jours",
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
                    'placeholder' => "Ex: 2",
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
                    'placeholder' => "Ex: pour 2€ -> 200",
                ],
            ])
            ->add('duration', TextType::class, [
                'label' => "Durée en jour",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Ex: 7",
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
