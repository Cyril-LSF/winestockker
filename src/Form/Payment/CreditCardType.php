<?php

namespace App\Form\Payment;

use App\Entity\CreditCard;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreditCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Propriétaire de la carte",
                'label_attr' => [
                    'class' => "text-light text-bold mb-0",
                ],
                'required' => true,
                'attr' => [
                    'class' => "credit-card-input",
                    'placeholder' => "Ex: John Doe",
                    'id' => "cno",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir le nom qui figure sur la carte",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le nom doit contenir minimum { limit } caractères",
                        'max' => 255,
                        'maxMessage' => "Le nom doit contenir au maximum { limit } caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z\-\s]*$/i',
                        'match' => true,
                        'message' => "Seul les lettres, les tirets et les espaces sont autorisés",
                    ]),
                ]
            ])
            ->add('number', TextType::class, [
                'label' => "Numéro de carte",
                'label_attr' => [
                    'class' => "text-light text-bold mb-0",
                ],
                'required' => true,
                'attr' => [
                    'class' => "credit-card-input",
                    'placeholder' => "XXXXXXXXXXXXXXXX",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un numéro de carte",
                    ]),
                    new Length([
                        'min' => 16,
                        'minMessage' => "Le numréo de carte doit contenir au minimum { limit } caractères",
                        'max' => 16,
                        'maxMessage' => "Le numéro de carte doit contenir au maximum { limit } caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d*$/',
                        'match' => true,
                        'message' => "Seul les chiffres sont autorisés",
                    ]),
                ],
            ])
            ->add('expiration', TextType::class, [
                'label' => "Expiration",
                'label_attr' => [
                    'class' => "text-light text-bold mb-0",
                ],
                'required' => true,
                'attr' => [
                    'class' => "credit-card-input",
                    'placeholder' => "Ex: 12/26",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une date d'expiration",
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => "L'expiration doit contenir minimum { limit } caractères",
                        'max' => 5,
                        'maxMessage' => "L'expiration doit contenir maximum { limit } caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^(\d{2})(\/)(\d{2})$/',
                        'match' => true,
                        'message' => "Le format doit être le suivant : 12/26",
                    ]),
                    new Callback([
                        'callback' => static function (?string $data, ExecutionContextInterface $context) {
                            if ($data) {
                                $data = explode('/', $data);
                                if ($data[0] < 1 || $data[0] > 12) {
                                    $context
                                        ->buildViolation("Veuillez saisir une date valide")
                                        ->addViolation();
                                    return;
                                }
                                $now = new DateTime();
                                $now = $now->format('m/y');
                                $now = explode('/', $now);
                                if (($data[0] < $now[0]) || ($data[1] < $now[1])) {
                                    $context
                                        ->buildViolation("La carte a expirée")
                                        ->addViolation();
                                }
                            }
                        }
                    ])
                ]
            ])
            ->add('securityCode', PasswordType::class, [
                'label' => "Cvv",
                'label_attr' => [
                    'class' => "text-light text-bold mb-0",
                ],
                'required' => true,
                'attr' => [
                    'class' => "credit-card-input",
                    'placeholder' => "Ex: 123",
                    'id' => "exp",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir un CVV",
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le CVV doit contenir minimum { limit } caractères",
                        'max' => 3,
                        'maxMessage' => "Le CVV doit contenir maximum { limit } caractères",
                    ]),
                    new Regex([
                        'pattern' => '/^\d*$/',
                        'match' => true,
                        'message' => "Seul les chiffres sont autorisés",
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreditCard::class,
        ]);
    }
}
