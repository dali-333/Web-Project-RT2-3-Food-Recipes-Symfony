<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuggestionController extends AbstractController
{
    #[Route('/suggest', name: 'app_suggest')]
    public function index(Request $request, RecipeRepository $recipeRepo, IngredientRepository $ingredientRepo): Response
    {
        $suggestions = [];
        $input = $request->query->get('ingredients', '');

        if ($input) {
            $userIngredients = array_map(
                fn($i) => strtolower(trim($i)),
                explode(',', $input)
            );
            $userIngredients = array_filter($userIngredients);

            $allRecipes = $recipeRepo->findAll();

            foreach ($allRecipes as $recipe) {
                $matchCount = 0;
                $totalIngredients = count($recipe->getRecipeIngredients());

                if ($totalIngredients === 0) continue;

                foreach ($recipe->getRecipeIngredients() as $ri) {
                    $name = strtolower($ri->getIngredient()->getName());
                    foreach ($userIngredients as $userIng) {
                        if (str_contains($name, $userIng) || str_contains($userIng, $name)) {
                            $matchCount++;
                            break;
                        }
                    }
                }

                if ($matchCount > 0) {
                    $suggestions[] = [
                        'recipe' => $recipe,
                        'matchCount' => $matchCount,
                        'totalIngredients' => $totalIngredients,
                        'matchPercent' => round(($matchCount / $totalIngredients) * 100),
                    ];
                }
            }

            usort($suggestions, fn($a, $b) => $b['matchCount'] - $a['matchCount']);
        }

        return $this->render('suggestion/index.html.twig', [
            'suggestions' => $suggestions,
            'input' => $input,
        ]);
    }
}