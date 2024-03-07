<?php

namespace App\Repository;

use App\Entity\Quantity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quantity>
 *
 * @method Quantity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quantity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quantity[]    findAll()
 * @method Quantity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuantityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quantity::class);
    }
/**
        *  Trouver les quantités et ingrédients associés à la recette
        */
        public function findByRecipeId($idRecipe): array
        {
            // On récupère la connexion à la base SQL afin de pouvoir gérer entièrement l'envoie de la requête
            $conn = $this->getEntityManager()->getConnection();
            // Etape 2 : on construit la requete DQL
            $query = 'SELECT * FROM quantity
                WHERE recipe_id =:idRecipe';
            // On prépare manuellement la requête
            $result = $conn->executeQuery($query, ['idRecipe' => $idRecipe]);
    
            return $result->fetchAllAssociative();
        }
//    /**
//     * @return Quantity[] Returns an array of Quantity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Quantity
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
