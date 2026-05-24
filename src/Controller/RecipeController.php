<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RecipeRepository;
use App\Repository\FavoriteRepository;
use App\Entity\Recipe;

final class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'app_recipes')]
    public function list(RecipeRepository $repo, Request $request): Response
    {
        $cuisine = $request->query->get('cuisine');
        $mealType = $request->query->get('mealType');
        $difficulty = $request->query->get('difficulty');

        $recipes = $repo->findByFilters($cuisine, $mealType, $difficulty);

        return $this->render('recipe/list.html.twig', [
            'controller_name' => 'RecipeController',
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipes/{id}', name: 'app_recipe_show')]
    public function show(Recipe $recipe, FavoriteRepository $favoriteRepo): Response
    {
        $isFavorited = false;
        if ($this->getUser()) {
            $isFavorited = $favoriteRepo->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe,
            ]) !== null;
        }

        return $this->render('recipe/show.html.twig', [
            'controller_name' => 'RecipeController',
            'recipe' => $recipe,
            'isFavorited' => $isFavorited,
        ]);
    }
}
