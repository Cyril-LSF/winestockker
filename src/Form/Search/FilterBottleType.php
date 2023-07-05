<?php

namespace App\Form\Search;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\BottleRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Countries;

class FilterBottleType extends AbstractType
{
    private BottleRepository $bottleRepository;
    private User $user;

    public function __construct(BottleRepository $bottleRepository)
    {
        $this->bottleRepository = $bottleRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->user = $options['user'];
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Par nom",
                ],
            ])
            ->add('origin', ChoiceType::class, [
                'choices' => $this->getChoices('getOrigin'),
                'required' => false,
                'placeholder' => "Par provenance",
                'attr' => [
                    'class' => "form-control",
                ],
            ])
            ->add('vine', ChoiceType::class, [
                'choices' => $this->getChoices('getVine'),
                'required' => false,
                'placeholder' => "Par cépage",
                'attr' => [
                    'class' => "form-control",
                ],
            ])
            ->add('enbottler', ChoiceType::class, [
                'choices' => $this->getChoices('getEnbottler'),
                'required' => false,
                'placeholder' => "Par embouteilleur",
                'attr' => [
                    'class' => "form-control",
                ],
            ])
            ->add('year', TextType::class, [
                'label' => "Par année",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' => "Par année",
                ],
            ])
            ->add('price', RangeType::class, [
                'label' => "Prix max",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 10000,
                    'class' => "slider h-100 rounded",
                    'value' => 0,
                    'step' => 100,
                ],
            ])
        ;
        if ($options['category']) {
            $builder
                ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where("c.author = $this->user");
                    },
                    'required' => false,
                    'placeholder' => "Par catégorie",
                    'attr' => [
                        'class' => "form-control",
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'user' => null,
            'category' => null,
        ]);
    }

    public function getChoices(string $method)
    {
        $bottles = $this->bottleRepository->findBy(['author' => $this->user]);
        $choices = [];
        foreach ($bottles as $bottle) {
            $choices[
                $method == 'getOrigin' ? Countries::getName($bottle->$method()) : $bottle->$method()
            ] = $bottle->$method();
        }

        return $choices;
    }
}
