<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /*
    *  Trouver les quantités et ingrédients associés à la recette
    */
    public function findCommentsByRecipeId($idRecipe): array
    {
        // On récupère la connexion à la base SQL afin de pouvoir gérer entièrement l'envoie de la requête
        $conn = $this->getEntityManager()->getConnection();
        // Etape 2 : on construit la requete DQL
        $query = 'SELECT * FROM comment
            WHERE recipe_id =:idRecipe';
        // On prépare manuellement la requête
        $result = $conn->executeQuery($query, ['idRecipe' => $idRecipe]);

        return $result->fetchAllAssociative();
    }
    /*
    * Récupère toutes les notes de la recette
    */
    public function ratesByRecipeId($idRecipe): array
    {
        // On récupère la connexion à la base SQL afin de pouvoir gérer entièrement l'envoie de la requête
        $conn = $this->getEntityManager()->getConnection();
        // Etape 2 : on construit la requete DQL
        $query = 'SELECT rate FROM comment
            WHERE recipe_id =:idRecipe';
        // On prépare manuellement la requête
        $result = $conn->executeQuery($query, ['idRecipe' => $idRecipe]);

        return $result->fetchAllAssociative();
    }
//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
