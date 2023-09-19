<?php

namespace App\Form\Cellar;

use App\Entity\Cellar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CellarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom *",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Ex: Cave 1",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un nom",
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => "Le nom doit contenir au minimum {{ limit }} caractères",
                        'max' => 255,
                        'maxMessage' => "Le nom doit contenir au maximum {{ limit }} caractères",
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cellar::class,
        ]);
    }
}
