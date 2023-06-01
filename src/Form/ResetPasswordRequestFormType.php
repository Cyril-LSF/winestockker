<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => "Email",
            'attr' => [
                'placeholder' => "E-Mail",
                'class' => "form-control rounded-0",
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
