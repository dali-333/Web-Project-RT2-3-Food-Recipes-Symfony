<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FavoriteRepository;
use App\Entity\Favorite;
use App\Entity\Recipe;

final class FavoriteController extends AbstractController
{
    #[Route('/favorites', name: 'app_favorites')]
    #[IsGranted('ROLE_USER')]
    public function index(FavoriteRepository $favoriteRepo): Response
    {
        $favorites = $favoriteRepo->findBy(['user' => $this->getUser()]);

        return $this->render('favorite/list.html.twig', [
            'controller_name' => 'FavoriteController',
            'favorites' => $favorites,
        ]);
    }

    #[Route('/favorites/toggle/{id}', name: 'app_favorite_toggle', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggle(Recipe $recipe, EntityManagerInterface $em, FavoriteRepository $favoriteRepo): Response
    {
        $existing = $favoriteRepo->findOneBy(['user' => $this->getUser(), 'recipe' => $recipe]);

        if ($existing) {
            $em->remove($existing);
        } else {
            $favorite = new Favorite();
            $favorite->setUser($this->getUser());
            $favorite->setRecipe($recipe);
            $favorite->setAddedAt(new \DateTimeImmutable());
            $em->persist($favorite);
        }
        $em->flush();

        return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
    }
}
