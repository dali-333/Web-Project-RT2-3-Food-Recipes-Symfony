<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\CuisineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizineController extends AbstractController
{
    #[Route('/quizine', name: 'app_quizine')]
    public function index(RecipeRepository $recipeRepo, CuisineRepository $cuisineRepo): Response
    {
        $recipes = $recipeRepo->findAll();
        $allCuisines = $cuisineRepo->findAll();

        $recipeData = [];
        foreach ($recipes as $recipe) {
            $recipeData[] = [
                'id'       => $recipe->getId(),
                'title'    => $recipe->getTitle(),
                'image'    => $recipe->getImageFilename(),
                'cuisine'  => $recipe->getCuisine()->getName(),
                'flag'     => $recipe->getCuisine()->getFlagEmoji(),
                'mealType' => $recipe->getMealType(),
            ];
        }

        $cuisineData = [];
        foreach ($allCuisines as $cuisine) {
            $cuisineData[] = [
                'name' => $cuisine->getName(),
                'flag' => $cuisine->getFlagEmoji(),
            ];
        }

        return $this->render('quizine/index.html.twig', [
            'recipes'  => json_encode($recipeData),
            'cuisines' => json_encode($cuisineData),
        ]);
    }
}