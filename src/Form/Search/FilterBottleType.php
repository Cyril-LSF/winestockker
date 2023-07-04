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
            // ->add('name', EntityType::class, [
            //     'class' => Bottle::class,
            //     'query_builder' => function (EntityRepository $er) use ($user) {
            //         return $er->createQueryBuilder('b')
            //             ->select('b.name')
            //             ->where("b.author = $user");
            //     },
            //     'required' => false,
            //     'attr' => [
            //         'class' => "form-control",
            //         'placeholder' => "Nom",
            //     ],
            // ])
            ->add('origin', ChoiceType::class, [
                'choices' => $this->getChoices('getOrigin'),
                'required' => false,
                'placeholder' => "Provenance",
                'attr' => [
                    'class' => "form-control",
                ],
            ])
            ->add('vine', ChoiceType::class, [
                'choices' => $this->getChoices('getVine'),
                'required' => false,
                'placeholder' => "Cépage",
                'attr' => [
                    'class' => "form-control",
                ],
            ])
            ->add('enbottler', ChoiceType::class, [
                'choices' => $this->getChoices('getEnbottler'),
                'required' => false,
                'placeholder' => "Embouteilleur",
                'attr' => [
                    'class' => "form-control",
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
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where("c.author = $this->user");
                },
                'required' => false,
                'placeholder' => "Catégorie",
                'attr' => [
                    'class' => "form-control",
                ]
            ])
            ->add('price', RangeType::class, [
                'label' => "Prix max",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'class' => "slider h-100 rounded",
                    'value' => 0,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'user' => null,
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
