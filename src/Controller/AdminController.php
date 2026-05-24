<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function dashboard(RecipeRepository $repo): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'recipes' => $repo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/recipe/new', name: 'app_admin_recipe_new')]
    public function newRecipe(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, IngredientRepository $ingredientRepository): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                    . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('recipes_images_directory'), $newFilename);
                $recipe->setImageFilename($newFilename);
            }

            // Handle ingredients
            foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                $ingredientName = $recipeIngredient->getIngredientName();

                if (!$ingredientName) continue;

                $ingredient = $ingredientRepository->findOneBy(['name' => $ingredientName])
                    ?? new Ingredient();
                $ingredient->setName($ingredientName);
                $em->persist($ingredient);

                $recipeIngredient->setIngredient($ingredient);
                $recipeIngredient->setRecipe($recipe);
                $em->persist($recipeIngredient);
            }

            $recipe->setAuthor($this->getUser());
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();

            $this->addFlash('success', 'Recipe published successfully!');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/recipe_form.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
            'edit' => false,
        ]);
    }

    #[Route('/recipe/{id}/edit', name: 'app_admin_recipe_edit')]
    public function editRecipe(Recipe $recipe, Request $request, EntityManagerInterface $em, IngredientRepository $ingredientRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                    . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('recipes_images_directory'), $newFilename);
                $recipe->setImageFilename($newFilename);
            }
            // Handle ingredients
            foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                $ingredientName = $recipeIngredient->getIngredientName();

                if (!$ingredientName) continue;

                $ingredient = $ingredientRepository->findOneBy(['name' => $ingredientName])
                    ?? new Ingredient();
                $ingredient->setName($ingredientName);
                $em->persist($ingredient);

                $recipeIngredient->setIngredient($ingredient);
                $recipeIngredient->setRecipe($recipe);
                $em->persist($recipeIngredient);
            }

            $em->flush();
            $this->addFlash('success', 'Recipe updated!');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/recipe_form.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
            'edit' => true,
        ]);
    }

    #[Route('/recipe/{id}/delete', name: 'app_admin_recipe_delete', methods: ['POST'])]
    public function deleteRecipe(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('warning', 'Recipe deleted.');
        return $this->redirectToRoute('app_admin');
    }
}