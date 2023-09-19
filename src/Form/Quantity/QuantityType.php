<?php

namespace App\Form\Quantity;

use App\Entity\Quantity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class QuantityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', TextType::class, [
                'label' => "Quantité *",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                ],
                'empty_data' => '0',
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une quantité",
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => "La quantité doit contenir au minimum {{ limit }} caractère",
                        'max' => 7,
                        'maxMessage' => "La quantité doit contenir au maximum {{ limit }} caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'match' => true,
                        'message' => "1 caractère minimum, seul les chiffres sont acceptés",
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quantity::class,
        ]);
    }
}
