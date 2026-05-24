<?php

namespace App\Controller;

use App\Entity\Cuisine;
use App\Repository\CuisineRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository, CuisineRepository $cuisineRepository): Response
    {
        $recipes = $recipeRepository->findBy([], ['createdAt' => 'DESC'], 6);
        $cuisines = $cuisineRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'recipes' => $recipes,
            'cuisines' => $cuisines,
        ]);
    }
}
