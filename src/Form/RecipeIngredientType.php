<?php

namespace App\Form;

use App\Entity\RecipeIngredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredientName', TextType::class, [
                'mapped' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g. Tomato',
                ],
                'label' => 'Ingredient',
            ])
            ->add('quantity', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g. 2 cups',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => RecipeIngredient::class]);
    }
}