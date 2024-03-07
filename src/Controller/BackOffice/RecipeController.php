<?php

namespace App\Controller\BackOffice;

use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Form\BackOffice\RecipeType as BackOfficeRecipeType;
use App\Repository\CommentRepository;
use App\Repository\IngredientRepository;
use App\Repository\QuantityRepository;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/recipe')]
class RecipeController extends AbstractController
{

    #[Route('/list', name: 'recipe_list')]
    public function list(RecipeRepository $recipeRepository): Response
    {

        $recipes = $recipeRepository->findAll();

        return $this->render('BackOffice/recipe/list.html.twig', [
            'recipes' => $recipes,
        ]);
    }



    #[Route('/add', name: 'recipe_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $quantity = new Quantity();

        $form = $this->createForm(BackOfficeRecipeType::class, $recipe);
        $form->handleRequest($request);
        $recipe->setCreatedAt(new DateTimeImmutable());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->persist($quantity);

            $entityManager->flush();
            $this->addFlash(
                'success',
                'La recette' . $recipe->getName() . 'a bien été créée !'
            );

            return $this->redirectToRoute('recipe_list');
        }

        return $this->render('BackOffice/recipe/add.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'recipe_show', methods: ['GET'])]

    public function show(Recipe $recipe): Response
    {
        return $this->render('BackOffice/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/edit/{id}', name: 'recipe_edit')]
    public function edit(Request $request, Recipe $recipe,  EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BackOfficeRecipeType::class, $recipe);
        $form->handleRequest($request);
        $recipe->setUpdateAt(new DateTimeImmutable());

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($recipe);

            $entityManager->flush();

            return $this->redirectToRoute('recipe_list');
        }

        return $this->render('BackOffice/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/delete', name: 'recipe_delete')]
    public function delete(Request $request, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager, QuantityRepository $quantityRepository, CommentRepository $commentRepository, $id): Response

    {

       // Je recupere la recette
       $recipe = $recipeRepository->find($id);

       //SUPPRESSION DES QUANTITÉS ASSOCIÉES A LA RECETTE

           //Je viens récupérer toutes les quantités associées à ma recette
           $quantitiesFromBdd = $quantityRepository->findByRecipeId($id);

           //Ici je viens boucler sur toutes les quantités
           foreach ($quantitiesFromBdd as $quantityFromBdd)
           {
               //Je viens récupérer l'id de ma quantité depuis ma bdd
               $quantityObject = $quantityRepository->find($quantityFromBdd['id']);
               //dd($quantityObject);
               //Je supprime l'objet quantité. 
               $entityManager->remove($quantityObject);
           } 
       //SUPPRESSION DES COMMENTAIRES ASSOCIÉES A LA RECETTE

           //Je viens récupérer tous les commentaires associés à ma recette
           $commentsFromBdd = $commentRepository->findCommentsByRecipeId($id);
           //Ici je viens boucler sur toutes les commentaires
           foreach ($commentsFromBdd as $commentFromBdd)
           {
               //Je viens récupérer l'id de mon commentaire depuis ma bdd
               $commentObject = $commentRepository->find($commentFromBdd['id']);
               //dd($commentObject);
               //Je supprime l'objet comment 
               $entityManager->remove($commentObject);
           }
           
       $entityManager->remove($recipe); //je la supprime

       $entityManager->flush();

            return $this->redirectToRoute('recipe_list');
        }
}

