<?php

namespace App\Repository;

use App\Entity\Ustensil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ustensil>
 *
 * @method Ustensil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ustensil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ustensil[]    findAll()
 * @method Ustensil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UstensilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ustensil::class);
    }

    /**
        *  Trouver les ustensiles associés à la recette
        */
    public function findByRecipeId($idRecipe): array
    {
        // On récupère la connexion à la base SQL afin de pouvoir gérer entièrement l'envoie de la requête
        $conn = $this->getEntityManager()->getConnection();
        // Etape 2 : on construit la requete
        $query = 'SELECT * FROM recipe_ustensil
            WHERE recipe_id =:idRecipe';
        // On prépare manuellement la requête
        $result = $conn->executeQuery($query, ['idRecipe' => $idRecipe]);

        return $result->fetchAllAssociative();
    }


//    public function findOneBySomeField($value): ?Ustensil
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
