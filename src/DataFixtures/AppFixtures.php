<?php

namespace App\DataFixtures;

use App\Entity\Cuisine;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Cuisines
        $cuisineData = [
            ['Italian', '🇮🇹'], ['French', '🇫🇷'], ['Japanese', '🇯🇵'],
            ['Mexican', '🇲🇽'], ['Indian', '🇮🇳'], ['Tunisian', '🇹🇳'],
            ['American', '🇺🇸'], ['Chinese', '🇨🇳'],
        ];
        $cuisines = [];
        foreach ($cuisineData as [$name, $flag]) {
            $c = (new Cuisine())->setName($name)->setFlagEmoji($flag);
            $manager->persist($c);
            $cuisines[$name] = $c;
        }

        // Admin user
        $admin = new User();
        $admin->setUsername('chef_admin')
              ->setEmail('admin@foodiehub.com')
              ->setRoles(['ROLE_ADMIN'])
              ->setCreatedAt(new \DateTimeImmutable())
              ->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Regular user
        $user = new User();
        $user->setUsername('foodie_user')
             ->setEmail('user@foodiehub.com')
             ->setRoles(['ROLE_USER'])
             ->setCreatedAt(new \DateTimeImmutable())
             ->setPassword($this->hasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // Helper to create or reuse ingredients
        $ingredientCache = [];
        $getIngredient = function(string $name) use (&$ingredientCache, $manager): Ingredient {
            $key = strtolower($name);
            if (!isset($ingredientCache[$key])) {
                $i = (new Ingredient())->setName($name);
                $manager->persist($i);
                $ingredientCache[$key] = $i;
            }
            return $ingredientCache[$key];
        };

        // Recipe data: [title, cuisine, mealType, difficulty, prepTime, cookTime, servings, image, description, instructions, [[ingredient, quantity]]]
        $recipes = [
            [
                'Spaghetti Carbonara', 'Italian', 'dinner', 'medium', 20, 15, 2, 'carbonara.jpg',
                'A creamy Roman pasta dish made with eggs, pecorino cheese, pancetta and black pepper.',
                "Cook spaghetti al dente.\nFry pancetta until crispy.\nMix eggs with grated pecorino.\nCombine hot pasta with pancetta off the heat.\nAdd egg mixture and toss quickly to coat.\nSeason generously with black pepper and serve immediately.",
                [['Spaghetti', '200g'], ['Pancetta', '100g'], ['Eggs', '3'], ['Pecorino Romano', '50g'], ['Black Pepper', '1 tsp']],
            ],
            [
                'Chicken Tacos', 'Mexican', 'lunch', 'easy', 15, 20, 4, 'tacos.jpg',
                'Juicy spiced chicken served in warm corn tortillas with fresh salsa and avocado.',
                "Season chicken with cumin, paprika, garlic powder and salt.\nCook chicken in a hot pan until done, about 7 minutes per side.\nShred the chicken with two forks.\nWarm tortillas in a dry pan.\nAssemble with chicken, salsa, avocado slices and cheddar.\nFinish with a squeeze of lime.",
                [['Chicken Breast', '500g'], ['Corn Tortillas', '8'], ['Avocado', '2'], ['Salsa', '150ml'], ['Cheddar', '80g'], ['Lime', '1'], ['Cumin', '1 tsp']],
            ],
            [
                'Shakshuka', 'Tunisian', 'breakfast', 'easy', 10, 20, 3, 'shakshuka.jpg',
                'Eggs poached in a rich, spiced tomato and pepper sauce. A North African breakfast classic.',
                "Sauté onion and bell pepper in olive oil until soft.\nAdd garlic, cumin, paprika and chili flakes.\nPour in crushed tomatoes and simmer for 10 minutes.\nMake wells in the sauce and crack eggs into them.\nCover and cook until egg whites are set but yolks still runny.\nGarnish with parsley and serve with crusty bread.",
                [['Eggs', '4'], ['Crushed Tomatoes', '400g'], ['Bell Pepper', '1'], ['Onion', '1'], ['Garlic', '3 cloves'], ['Cumin', '1 tsp'], ['Paprika', '1 tsp']],
            ],
            [
                'Tonkotsu Ramen', 'Japanese', 'dinner', 'hard', 30, 180, 4, 'ramen.jpg',
                'Rich, creamy pork bone broth with springy noodles, tender chashu pork and a soft boiled marinated egg.',
                "Blanch pork bones then simmer for 3 hours to make broth.\nSeason the broth with soy sauce, mirin and salt.\nMarinate and slow cook pork belly for the chashu.\nSoft boil eggs for 7 minutes then marinate in soy and mirin.\nCook ramen noodles according to package.\nAssemble bowls: broth, noodles, sliced chashu, halved egg, nori, green onions.",
                [['Pork Bones', '1kg'], ['Ramen Noodles', '400g'], ['Pork Belly', '500g'], ['Eggs', '4'], ['Soy Sauce', '4 tbsp'], ['Nori', '4 sheets'], ['Green Onions', '4']],
            ],
            [
                'Butter Croissants', 'French', 'breakfast', 'hard', 120, 20, 8, 'croissant.jpg',
                'Flaky, buttery French croissants with perfectly laminated layers. Worth every minute of effort.',
                "Mix flour, sugar, salt and yeast with milk to form a dough.\nRest the dough overnight in the fridge.\nBeat butter into a flat slab and encase in the dough.\nFold and roll the dough 3 times with 30 min rests between.\nCut triangles and roll them up from base to tip.\nProve for 2 hours until puffy.\nBrush with egg wash and bake at 200°C for 18 minutes.",
                [['Flour', '500g'], ['Butter', '300g'], ['Milk', '300ml'], ['Sugar', '50g'], ['Yeast', '7g'], ['Salt', '10g'], ['Eggs', '1']],
            ],
            [
                'Chicken Biryani', 'Indian', 'dinner', 'medium', 40, 45, 6, 'biryani.jpg',
                'Fragrant basmati rice layered with spiced chicken, caramelized onions and saffron.',
                "Marinate chicken in yogurt, garam masala, turmeric and ginger garlic paste for 2 hours.\nFry sliced onions until deep golden and crispy.\nCook the marinated chicken until done.\nParboil basmati rice with whole spices until 70% cooked.\nLayer rice over chicken in a heavy pot.\nTop with fried onions and saffron milk.\nSeal with foil and cook on low heat for 25 minutes.",
                [['Chicken', '1kg'], ['Basmati Rice', '500g'], ['Yogurt', '200ml'], ['Onions', '3'], ['Saffron', '1 pinch'], ['Garam Masala', '2 tsp'], ['Ginger', '1 thumb']],
            ],
            [
                'Smash Burger', 'American', 'lunch', 'easy', 10, 15, 2, 'burger.jpg',
                'Crispy-edged smashed beef patties with melted American cheese on a toasted brioche bun.',
                "Divide ground beef into 80g balls.\nHeat a cast iron pan until smoking hot.\nPlace beef ball on pan and immediately smash flat with a spatula.\nSeason with salt and pepper.\nCook 2 minutes until edges are crispy, flip and add cheese.\nToast brioche buns in butter.\nAssemble with patty, pickles, onion, ketchup and mustard.",
                [['Ground Beef', '320g'], ['American Cheese', '4 slices'], ['Brioche Buns', '2'], ['Pickles', '6 slices'], ['Onion', '1/2'], ['Butter', '1 tbsp']],
            ],
            [
                'Tiramisu', 'Italian', 'dessert', 'medium', 30, 0, 8, 'tiramisu.jpg',
                'The classic Italian no-bake dessert with espresso-soaked ladyfingers and mascarpone cream.',
                "Brew strong espresso and let it cool. Add a splash of coffee liqueur.\nWhisk egg yolks with sugar until pale and thick.\nFold in mascarpone until smooth.\nWhip cream to soft peaks and fold into mascarpone mixture.\nDip ladyfingers briefly in espresso and line a dish.\nSpread half the cream mixture over the biscuits.\nRepeat layers and dust generously with cocoa powder.\nRefrigerate for at least 4 hours before serving.",
                [['Mascarpone', '500g'], ['Ladyfingers', '200g'], ['Eggs', '4'], ['Espresso', '300ml'], ['Sugar', '80g'], ['Heavy Cream', '200ml'], ['Cocoa Powder', '2 tbsp']],
            ],
        ];

        foreach ($recipes as [$title, $cuisine, $mealType, $diff, $prep, $cook, $servings, $image, $desc, $instructions, $ingredients]) {
            $r = new Recipe();
            $r->setTitle($title)
              ->setCuisine($cuisines[$cuisine])
              ->setMealType($mealType)
              ->setDifficulty($diff)
              ->setPrepTime($prep)
              ->setCookTime($cook)
              ->setServings($servings)
              ->setImageFilename($image)
              ->setDescription($desc)
              ->setInstructions($instructions)
              ->setAuthor($admin)
              ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($r);

            foreach ($ingredients as [$ingName, $quantity]) {
                $ri = new RecipeIngredient();
                $ri->setIngredient($getIngredient($ingName))
                   ->setQuantity($quantity)
                   ->setRecipe($r);
                $manager->persist($ri);
            }
        }

        $manager->flush();
    }
}