<?php

namespace App\Form\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => "Objet *",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un objet",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "L'objet doit contenir minimum {{ limit }}",
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => "Message *",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'rows' => 6,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un message",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le message doit contenir minimum {{ limit }} caractères",
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}