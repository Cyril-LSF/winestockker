<?php

namespace App\Form\Bottle;

use App\Entity\Bottle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class BottleType extends AbstractType
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
                    'placeholder' => "Nom",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un nom",
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Le nom doit contenir au maximum {{ limit }} caractères",
                    ]),
                ],
            ])
            ->add('year', TextType::class, [
                'label' => "Année",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Année",
                ],
                'constraints' => [
                    new Length([
                        'max' => 5,
                        'maxMessage' => "L'année doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d*$/',
                        'match' => true,
                        'message' => "Seul les chiffres sont autorisés",
                    ]),
                ],
            ])
            ->add('origin', CountryType::class, [
                'label' => "Provenance",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control text-muted",
                ],
                'placeholder' => 'Choisissez un pays',
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => "Le pays doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z]*$/i',
                        'match' => true,
                        'message' => "Seul les lettres sont autorisées",
                    ]),
                ],
            ])
            ->add('vine', TextType::class, [
                'label' => "Cépage",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Céapage",
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "Le cépage doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z\s\-éèêëâäîï]*$/i',
                        'match' => true,
                        'message' => "Seul les lettres sont autorisées",
                    ]),
                ],
            ])
            ->add('enbottler', textType::class, [
                'label' => "Embouteilleur",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Embouteilleur",
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => "L'embouteilleur doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z\s\-éèêëâäîï]*$/i',
                        'match' => true,
                        'message' => "Seul les lettres sont autorisées",
                    ]),
                ],
            ])
            ->add('price', textType::class, [
                'label' => "Prix",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Prix",
                ],
                'constraints' => [
                    new Length([
                        'max' => 10,
                        'maxMessage' => "Le prix doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d*$/',
                        'match' => true,
                        'message' => "Seul les chiffres sont autorisés",
                    ]),
                ],
            ])
            //->add('cellars')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bottle::class,
        ]);
    }
}
