<?php

namespace App\Form\Category;

use App\Entity\Bottle;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddBottleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('bottles', EntityType::class, [
                'class' => Bottle::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('b')
                        ->where("b.author = $user");
                },
                'label' => "Bouteilles",
                'label_attr' => [
                    'class' => "form-label",
                ],
                'required' => true,
                'attr' => [
                    'class' => "",
                ],
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez selectionner une ou plusieurs bouteilles",
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'user' => null,
        ]);
    }
}
