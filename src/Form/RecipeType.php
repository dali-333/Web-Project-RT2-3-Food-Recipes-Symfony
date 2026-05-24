<?php

namespace App\Form;

use App\Entity\Cuisine;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('instructions', TextareaType::class)
            ->add('difficulty', ChoiceType::class, [
                'choices' => [
                    'Easy' => 'easy',
                    'Medium' => 'medium',
                    'Hard' => 'hard',
                ],
            ])
            ->add('mealType', ChoiceType::class, [
                'choices' => [
                    'Breakfast' => 'breakfast',
                    'Lunch' => 'lunch',
                    'Dinner' => 'dinner',
                    'Snack' => 'snack',
                    'Dessert' => 'dessert',
                    'Salad' => 'salad',
                    'Soup' => 'soup',
                    'Appetizer' => 'appetizer',
                    'Beverage' => 'beverage',
                ],
            ])
            ->add('cuisine', EntityType::class, [
                'class' => Cuisine::class,
                'choice_label' => 'name',
            ])
            ->add('prepTime', IntegerType::class)
            ->add('cookTime', IntegerType::class)
            ->add('servings', IntegerType::class)
            ->add('imageFileName', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
