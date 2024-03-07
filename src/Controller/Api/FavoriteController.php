<?php

namespace App\Controller\Api;


use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class FavoriteController extends AbstractController
{

    /**
     * Supprime un favori
     *
     */
    #[Route("/api/favorite/delete/{id}", name:"api_favorit_delete", methods: ["DELETE"])]
public function deleteFavorites(RecipeRepository $recipeRepository, $id, EntityManagerInterface $entityManager,Request $request)
    {
        $user = $this->getUser();

        $recipe = $recipeRepository->find($id);
        $user->removeRecipesFavorite($recipe);
        
      
        $entityManager->flush(); //j'enregistre en database

        return $this->json(
            $recipe,
            200,
            ['Favori supprimé'],
            ['groups' => 'get_users'] 
        );
    }

    /**
     * Ajoute un favori
     *
     * @return Response
     */
    #[Route("/api/favorite/add/{idRecipe}", name:"api_favorite_add", methods: ["POST"])]
    public function addFavorite(Request $request, $idRecipe, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager)
    {
        // Récupérer l'utilisateur actuellement authentifié (vous devrez implémenter la logique d'authentification)
        $user = $this->getUser();

        // Récupérer la recette à partir de son ID
        $recipe = $recipeRepository->find($idRecipe);

        // Ajouter la recette aux favoris de l'utilisateur
        $user->addRecipesFavorite($recipe);

        // Persister les changements
        $entityManager->persist($recipe);
        $entityManager->flush();

        return $this->json(
            $recipe,
            200,
            ['Favori ajouté'],
            ['groups' => 'get_users'] 
        );
    }
}



