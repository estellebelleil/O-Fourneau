<?php

namespace App\Controller\BackOffice;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Form\BackOffice\CategoryType as BackOfficeCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// je veux que toutes mes routes de ce controller commmencer par /back/category
#[Route('/back/category')]
class CategoryController extends AbstractController
{
    // je crée une route pour afficher toutes les catégories
    #[Route('/list', name: 'category_list')]
    public function list(CategoryRepository $categoryRepository): Response
    {
        // ici je recupère toutes les catégories 
        $categories = $categoryRepository->findAll();
    //   je les envoies à ma vue
        return $this->render('BackOffice/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }


    // je crée une route pour ajouter une catégorie
    #[Route('/add', name: 'category_add', methods: ['GET', 'POST'])] // je crée une route pour ajouter une catégorie en get qui affiche le formulaire et en post qui permet de l'envoyer
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category(); // je crée une nouvelle catégorie
        $form = $this->createForm(BackOfficeCategoryType::class, $category); // je crée un formulaire
        $form->handleRequest($request); // je recupère les données du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category); // j'enregistre en base de données
            $entityManager->flush();
            // je crée un message flash pour informer l'utilisateur que la catégorie a bien été créee
            $this->addFlash(
                'success',
                'La catégorie' . $category->getName() . 'a bien été créée !'
            );

            return $this->redirectToRoute('category_list');
        }

        return $this->render('BackOffice/category/add.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }


    // ici j'affiche la page de la catégorie selectionnée
    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category,RecipeRepository $recipe): Response // je récupère la catégorie selectionée
    {
        
        return $this->render('BackOffice/category/show.html.twig', [
            'category' => $category, //je renvoie la catégorie à la vue
            'recipes' => $recipe->findBy(['category' => $category]) // je renvoie les recettes de la catégorie à la vue
           
        ]);
    }


    // je crée une route pour mettre à jour une catégorie
    #[Route('/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BackOfficeCategoryType::class, $category); //BackOfficeCategoryType est le formulaire que j'ai crée pour le backoffice et $category est la catégorie que je veux éditer
        $form->handleRequest($request);

    // si le formulaire est soumis et valide je flush (j'enregiste en database) et je redirige vers la liste des catégories
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('category_list');
        }
    // si le formulaire n'a pas été soumis je renvoie à la page d'édition
        return $this->render('BackOffice/category/edit.html.twig', [
            'category' => $category,  //je renvoie la catégorie à la vue
            'form' => $form, //je renvoie le formulaire à la vue 
        ]);
    }


    // je crée une route pour supprimer une catégorie
    #[Route('/{id}', name: 'category_delete')]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response 
   // si le token csrf est valide je supprime la catégorie et je redirige vers la liste des catégories
   // le token csrf est un token qui permet de vérifier que le formulaire a bien été soumis par le user et non par un robot
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);// je supprime la catégorie de la database
            $entityManager->flush();// j'enregistre en database
        }

        return $this->redirectToRoute('category_list');
    }
}
