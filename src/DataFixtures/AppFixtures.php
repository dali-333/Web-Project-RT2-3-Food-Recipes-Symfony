<?php

namespace App\DataFixtures;

use App\Entity\Cuisine;
use App\Entity\Recipe;
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
            $c = new Cuisine();
            $c->setName($name)->setFlagEmoji($flag);
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

        // Sample recipes
        $recipes = [
            ['Spaghetti Carbonara', 'Italian', 'dinner', 'medium', 20, 15, 2,
             'A creamy Roman pasta with eggs, cheese and pancetta.',
             "1. Cook spaghetti al dente.\n2. Fry pancetta until crisp.\n3. Mix eggs and pecorino.\n4. Combine pasta with pancetta off heat.\n5. Add egg mix and toss quickly.\n6. Season with black pepper and serve."],
            ['Chicken Tacos', 'Mexican', 'lunch', 'easy', 15, 20, 4,
             'Juicy seasoned chicken with fresh toppings in warm tortillas.',
             "1. Season chicken with cumin, paprika and garlic.\n2. Cook chicken in a pan until done.\n3. Shred the chicken.\n4. Warm tortillas.\n5. Assemble with chicken, salsa, avocado and cheese."],
            ['Shakshuka', 'Tunisian', 'breakfast', 'easy', 10, 20, 3,
             'Eggs poached in spiced tomato sauce — a North African classic.',
             "1. Sauté onion and peppers.\n2. Add garlic, cumin and paprika.\n3. Add crushed tomatoes and simmer.\n4. Make wells and crack eggs in.\n5. Cover and cook until eggs are set.\n6. Garnish with parsley and serve with bread."],
            ['Ramen', 'Japanese', 'dinner', 'hard', 30, 180, 4,
             'Rich tonkotsu broth with noodles, pork belly and soft boiled eggs.',
             "1. Simmer pork bones for 3 hours.\n2. Marinate and cook pork belly.\n3. Prepare tare seasoning.\n4. Soft boil eggs and marinate.\n5. Cook noodles separately.\n6. Assemble bowls with broth, noodles and toppings."],
        ];

        foreach ($recipes as [$title, $cuisine, $mealType, $diff, $prep, $cook, $servings, $desc, $instructions]) {
            $r = new Recipe();
            $r->setTitle($title)
              ->setCuisine($cuisines[$cuisine])
              ->setMealType($mealType)
              ->setDifficulty($diff)
              ->setPrepTime($prep)
              ->setCookTime($cook)
              ->setServings($servings)
              ->setDescription($desc)
              ->setInstructions($instructions)
              ->setAuthor($admin)
              ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($r);
        }

        $manager->flush();
    }
}