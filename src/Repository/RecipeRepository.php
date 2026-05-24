<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findByFilters(?int $cuisineId, ?string $mealType, ?string $difficulty, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.cuisine', 'c')
            ->addSelect('c')
            ->orderBy('r.createdAt', 'DESC');

        if ($cuisineId) {
            $qb->andWhere('c.id = :cuisine')->setParameter('cuisine', $cuisineId);
        }
        if ($mealType) {
            $qb->andWhere('r.mealType = :mealType')->setParameter('mealType', $mealType);
        }
        if ($difficulty) {
            $qb->andWhere('r.difficulty = :difficulty')->setParameter('difficulty', $difficulty);
        }
        if ($search) {
            $qb->andWhere('r.title LIKE :search OR r.description LIKE :search OR c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Recipe[] Returns an array of Recipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recipe
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
