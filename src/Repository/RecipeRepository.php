<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findBySearch($keyword)
    {
        // Étape 1 : nous appelons le manager d'entités
        $manager = $this->getEntityManager();
        // Etape 2 : on construit la requete DQL
        $query = $manager->createQuery(
            'SELECT r
            FROM App\Entity\Recipe r
            WHERE r.name LIKE :keyword' )
            ->setParameter(':keyword','%'. $keyword .'%');

        // Etape 3 : j'execute la requête DQL et je retourne le resultat
        return $query->getResult();
        
    }
    /**
     * Génère une recette au hasard
     *
     * @return void
     */
    public function getRandomRecipe()
    {
        
        // On récupère une recette aléatoire
        $sql = "SELECT * FROM recipe
        ORDER BY RAND()
        LIMIT 1";
        $manager = $this->getEntityManager();
        $conn = $manager->getConnection();
        $resultSet = $conn->executeQuery($sql);

        $recipe = $resultSet->fetchAssociative();
        //dd($recipe);
        //On récupère les commentaires lié à cette recette
        $sql = "SELECT * FROM comment WHERE recipe_id = :id";
        $result = $conn->executeQuery($sql, ['id' => $recipe['id']]);
        $comments = $result -> fetchAllAssociative();
        // On ajoute les commentaires à la recette
        $recipe['comments'] = $comments;
        // on retourne le résultat
        return $recipe;

    }

    public function getRandomRecipeWithUsername()
    {
            $manager = $this->getEntityManager();
            $conn = $manager->getConnection();
        
            // On récupère une recette aléatoire
            $sql = "SELECT * FROM recipe
                    ORDER BY RAND()
                    LIMIT 1";
        
            $resultSet = $conn->executeQuery($sql);
            $recipe = $resultSet->fetchAssociative();
        
            if (!$recipe) {
                return null; // Aucune recette trouvée
            }
        
            // On récupère les commentaires liés à cette recette
            $sql = "SELECT c.*, u.name AS name
                    FROM comment c
                    LEFT JOIN user u ON c.user_id = u.id
                    WHERE c.recipe_id = :recipe_id";
        
            $result = $conn->executeQuery($sql, ['recipe_id' => $recipe['id']]);
            $comments = $result->fetchAllAssociative();
        
            // Ajout des commentaires à la recette
            $recipe['comments'] = $comments;
        
            return $recipe;
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

}
