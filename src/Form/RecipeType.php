<?php

namespace App\Form;

use App\Entity\Cuisine;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('recipeIngredients', CollectionType::class, [
                'entry_type' => RecipeIngredientType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control', 'rows' => 3]])
            ->add('instructions', TextareaType::class, ['attr' => ['class' => 'form-control', 'rows' => 6]])
            ->add('difficulty', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices' => ['Easy' => 'easy', 'Medium' => 'medium', 'Hard' => 'hard'],
            ])
            ->add('mealType', ChoiceType::class, [
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Breakfast' => 'breakfast', 'Lunch' => 'lunch',
                    'Dinner' => 'dinner', 'Snack' => 'snack', 'Dessert' => 'dessert',
                ],
            ])
            ->add('cuisine', EntityType::class, [
                'class' => Cuisine::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('prepTime', IntegerType::class, ['attr' => ['class' => 'form-control']])
            ->add('cookTime', IntegerType::class, ['attr' => ['class' => 'form-control']])
            ->add('servings', IntegerType::class, ['attr' => ['class' => 'form-control']])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Recipe::class]);
    }
}