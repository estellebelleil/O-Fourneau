<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\Ustensil;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\IngredientRepository;
use App\Repository\QuantityRepository;
use App\Repository\RecipeRepository;
use App\Repository\TagRepository;
use App\Repository\UstensilRepository;
use App\Service\MySlugger;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecipeController extends AbstractController
{
    /**
     * RECUPERE TOUTES LES RECETTES
     * API
     */
    #[Route('api/recipe/list', name: 'api_recipe_list', methods: ['GET'])]
    public function list(RecipeRepository $recipeRepository)
    {
        // Je recupere toutes les recettes
        $recipes = $recipeRepository->findAll();
        //je les envoie à ma vue
        return $this->json(
            $recipes,
            200,
            ['Données envoyées'],
            ['groups' => 'get_recipes'] // je veux que le groupe get_recipes soit utilisé
        );
    }

    /**
     * RECUPERE UNE RECETTE
     * API
     */
    #[Route('api/recipe/show/{id}', name: 'api_recipe_show', methods: ['GET'])]
    public function show(RecipeRepository $recipeRepository, $id)
    {
        $recipe = $recipeRepository->find($id); // je recupere l'id de la recette
        //$recipe = $recipeRepository->findOneBy(['slug' => $slug]);

        return $this->json(
            $recipe,
            200,
            [],
            ['groups' => 'get_recipes']
        );
    }
        /**
         * RECUPERE UNE RECETTE ALEATOIRE
         *
         * @return Response
         */
        #[Route('api/recipe/random', name: 'api_recipe_random', methods: ['GET'])]
        public function randomRecipe(RecipeRepository $recipeRepository)
        {
            $randomRecipe = $recipeRepository->getRandomRecipeWithUsername(); // je recupere l'id de la recette
           // dd($randomRecipe);
            return $this->json(
                $randomRecipe,
                200,
                [],
                ['groups' => 'get_recipes']
            );
        }



    /**
     * CREER UNE RECETTE via un formulaire envoyé en JSON
     *
     * @return Response
     */
    #[Route('api/recipe/add', name: 'api_recipe_add', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager, MySlugger $slugger,SerializerInterface $serializer, IngredientRepository $ingredientRepository, UstensilRepository $ustensilRepository, TagRepository $tagRepository, CategoryRepository $categoryRepository, QuantityRepository $quantityRepository): Response
    {
        $content = $request->getContent(); // je récupère le contenu de la requête
        $data = json_decode($content, true); // je décode le contenu JSON en tableau associatif
        $recipe = new Recipe();
        $recipe->setName($data['name']);
        $recipe->setSlug($slugger->slugify($recipe->getName()));
        $recipe->setDescription($data['description']);
        $recipe->setPicture($data['picture']);
        $recipe->setStep($data['step']);
        $recipe->setCreatedAt(new DateTimeImmutable());
        
            //AJOUTE DES USTENSILS DANS MA RECETTE CRÉÉE
            if (isset($data['ustensil']) && is_array($data['ustensil'])) {
                $ustensils = []; // Créer un tableau pour stocker les objets Ustensil
                foreach ($data['ustensil'] as $ustensilId) {
                    $ustensil = $ustensilRepository->find($ustensilId);
                    //$ustensil = $entityManager->getRepository(Ustensil::class)->find($ustensilId); // Récupérer l'ustensil depuis la base de données
                    if ($ustensil) {
                        $ustensils[] = $ustensil; // Ajouter l'ustensil trouvé au tableau
                    }
                }
                foreach($ustensils as $ustensil)
                {
                    $recipe->addUstensil($ustensil);
                }
            }
            //AJOUTE DES TAGS DANS MA RECETTE CRÉÉE
            if (isset($data['tag']) && is_array($data['tag'])) {
                $tags = []; // Créer un tableau pour stocker les objets tag
                foreach ($data['tag'] as $tagId) {
                    $tag = $tagRepository->find($tagId);
                    //$tag = $entityManager->getRepository(tag::class)->find($tagId); // Récupérer l'tag depuis la base de données
                    if ($tag) {
                        $tags[] = $tag; // Ajouter le tag trouvé au tableau
                    }
                    }
                foreach($tags as $tag)
                    {
                        $recipe->addtag($tag);
                    }
                }

           //AJOUTE UNE CATEGORIE DANS MA RECETTE CRÉÉE
            if (isset($data['category']) && is_array($data['category'])) {
            foreach ($data['category'] as $categoryId) {
                $category = $categoryRepository->find($categoryId);
                //$category = $entityManager->getRepository(category::class)->find($categoryId); // Récupérer l'category depuis la base de données
                $recipe->setCategory($category);
                }
            }

           //AJOUTE UN USER DANS MA RECETTE CRÉÉE
            $user = $this->getUser();
            $recipe->setUser($user);

            //AJOUTE UNE QUANTITE DANS MA RECETTE CRÉÉE
            if (isset($data['quantities']) && is_array($data['quantities'])) {
                foreach ($data['quantities'] as $quantityData) {
                    // Récupérer l'ingrédient depuis la base de données en fonction de son identifiant
                    $ingredient = $ingredientRepository->find($quantityData['ingredient']['id']);
                    
                    if ($ingredient) {
                        // Créer une nouvelle instance de Quantity
                        $quantity = new Quantity();
                        $quantity->setIngredient($ingredient);
                        $quantity->setQuantity($quantityData['quantity']);
                        $entityManager->persist($quantity);
                        // Ajouter la quantité à la recette en utilisant la méthode addQuantity()
                        $recipe->addQuantity($quantity);
                    }
                    }
                }
        $entityManager->persist($recipe);

        $errorFields = $validator->validate($recipe);
        //S'il y a une erreur
        if (count($errorFields) > 0) {

            return $this->json($errorFields, 500, ['message' => 'error']);
        }
        else
        {
            $entityManager->flush();
            return $this->json(
                $recipe, 
                201, 
                [], 
                ['groups' => 'get_recipes']); //je retourne le code Json avec la nouvelle recette et le code de la requête
        }

    }


    /**
     * MODIFIER UNE RECETTE via un formulaire envoyé en JSON
     *
     * @return Response
     */
    // méthode pour modifier une recette avec déserialisation
    #[Route('api/recipe/edit/{id}', name: 'api_recipe_edit', methods: ['PUT'])]
    public function edit(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager, SerializerInterface $serializer, RecipeRepository $recipeRepository, $id, IngredientRepository $ingredientRepository, UstensilRepository $ustensilRepository, TagRepository $tagRepository, CategoryRepository $categoryRepository, QuantityRepository $quantityRepository, MySlugger $slugger): Response
    {

        $recipe = $recipeRepository->find($id); // je recupere la recette
        $user = $this->getUser();
        $content = $request->getContent();
        $data = json_decode($content, true); // je décode le contenu JSON
        
        //Ici je viens d'abord vérifier si l'utilisateur qui souhaite modifier la recette est bien celui qui l'a créée ou l'admin. S'il ne l'est pas, il a un message d'erreur
        if ($user !== $recipe->getUser() && !$this->isGranted('ROLE_ADMIN')) 
        {
            throw $this->createAccessDeniedException('Non autorisé.');
        }
        else
        {
            $recipe->setName($data['name']);
            $recipe->setDescription($data['description']);
            $recipe->setSlug($slugger->slugify($recipe->getName()));
            $recipe->setPicture($data['picture']);
            $recipe->setStep($data['step']);
            $recipe->setUpdateAt(new DateTimeImmutable());

            //Récupération des notes moyennes de la recette
            $rateFromComments = $recipe->getRate();
            //Je repasse la même note déjà trouvée.
            $recipe->setRate($rateFromComments);



            
            // EDITER LES QUANTITÉS D INGREDIENT (AJOUT/SUPPRESSION)
            //Ici je viens récupérer par l'id de la recette, les quantités associées depuis mon entité entité
            $quantitiesFromBdd = $quantityRepository->findByRecipeId($id);
            //Ici je viens récupérer les valeurs envoyées part ma request de quantities
            $quantitiesfromRequest =  $data['quantities'];
                //dd($quantitiesfromRequest);
                //Ici je viens boucler sur toutes les quantités associées à cette recette
                foreach ($quantitiesFromBdd as $quantityFromBdd)
                {
                    //je définie une variable find. 
                    $find = false;
                    //Je viens boucler sur toutes les quantités données dans ma requete                
                    foreach($quantitiesfromRequest as $key=>$quantityFromRequest)
                    {
                        //Je viens récupérer chaque ingrédient associé à l'id donné dans ma requete et toutes ses infos. 
                        $ingredient = $ingredientRepository->find($quantityFromRequest['ingredient']['id']);

                        //Si l'id de l'ingrédient de ma bdd est égal à l'id de l'ingrédient envoyé dans ma requete est équivalent
                        if ($quantityFromBdd['ingredient_id'] === $quantityFromRequest['ingredient']['id'])
                            {
                                //alors je passe mon find en true
                                $find = true;
                                //Si ma quantité dans ma bdd n'est pas égale à la quantité de ma requete
                                if($quantityFromBdd['quantity'] !== $quantityFromRequest['quantity'])
                                {
                                    //Je viens récupéré dans ma Bdd l'id de ma quantité
                                    $quantityObject = $quantityRepository->find($quantityFromBdd['id']);
                                    //Je viens remplacer sa valeur par la valeur de mon champ quantité de ma requête
                                    $quantityObject->setQuantity($quantityFromRequest['quantity']);
                                    //J'enregistre les modifications en bdd
                                    $entityManager->persist($quantityObject);
                                }
                                // Si l'élement a été trouvé, on le supprime de la liste des quantités à vérifier. Ici grace à cette fonction j'appelle le tableau quantities, je lui donne l'ordre de suppression et j'informe vouloir supprimer un élément du tableau depuis l'indice key
                                array_splice($quantitiesfromRequest,$key,1);
                                //J'arrete la boucle si je suis rentrée dans ma condition primaire. 
                                break;
                            }
                    }
                    //Si je ne reçois aucune information sur la quantité et l'ingrédient depuis ma requete find=false
                    if(!$find) {
                        //Je viens récupérer l'id de ma quantité depuis ma bdd
                        $quantityObject = $quantityRepository->find($quantityFromBdd['id']);  
                        //Je supprime l'objet quantité. 
                        $entityManager->remove($quantityObject);
                    }
                }
                // Si il reste des élements dans le tableau, c'est qu'il faut les ajouter à la BDD.
                if(count($quantitiesfromRequest)){
                    //Je boucle sur mon tableau de nouvelles données à ajouter
                    foreach($quantitiesfromRequest as $quantityToAdd){
                        //Je viens récupérer toutes les informations associées à l'ingrédient demandé dans ma requête
                        $ingredient = $ingredientRepository->find($quantityToAdd['ingredient']['id']);
                        //Si l'ingrédient existe en bdd
                        if ($ingredient) {
                            // Créer une nouvelle instance de Quantity
                            $quantity = new Quantity();
                            //Je lui ajoute l'ingrédient demandé
                            $quantity->setIngredient($ingredient);
                            //Je lui ajoute la quantité demandée dans ma requête
                            $quantity->setQuantity($quantityToAdd['quantity']);
                            //Je persiste le nouvel objet
                            $entityManager->persist($quantity);
                            // Ajouter la quantité à la recette en utilisant la méthode addQuantity()
                            $recipe->addQuantity($quantity);
                        }
                    }
                }

            //EDITER UN USTENSIL (AJOUT/SUPPRESSION)
            $ustensils = $ustensilRepository->findByRecipeId($id);
                foreach ($ustensils as $ustensilFromBdd) {
                    $find = false;
                    foreach ($data['ustensil'] as $ustensilFromRequest) 
                        {
                        if($ustensilFromBdd['ustensil_id'] == $ustensilFromRequest['id'])
                            {
                                $find = true;
                                break;
                            }
                        }
                        if($find == false)
                        {
                            $ustensil = $ustensilRepository->find($ustensilFromBdd['ustensil_id']);
                            $recipe->removeUstensil($ustensil);
                        }}

                        if (isset($data['ustensil']) && is_array($data['ustensil'])) {
                            $ustensils = []; // Créer un tableau pour stocker les objets Ustensil
                            foreach ($data['ustensil'] as $ustensilId) {
                                $ustensil = $ustensilRepository->find($ustensilId);
                                //$ustensil = $entityManager->getRepository(Ustensil::class)->find($ustensilId); // Récupérer l'ustensil depuis la base de données
                                if ($ustensil) {
                                    $ustensils[] = $ustensil; // Ajouter l'ustensil trouvé au tableau
                                }
                            }
                            foreach($ustensils as $ustensil)
                            {
                                $recipe->addUstensil($ustensil);
                            }
                        } 


            //EDITER UN TAG (AJOUT/SUPPRESSION)
            $tags = $tagRepository->findByRecipeId($id);
                foreach ($tags as $tagFromBdd) {
                $find = false;
                    foreach ($data['tag'] as $tagFromRequest) 
                        {
                        if($tagFromBdd['tag_id'] == $tagFromRequest['id'])
                            {
                                $find = true;
                                break;
                            }
                        }
                        if($find == false)
                        {
                            $tag = $tagRepository->find($tagFromBdd['tag_id']);
                            $recipe->removetag($tag);
                        }}

                        if (isset($data['tag']) && is_array($data['tag'])) {
                            $tags = []; // Créer un tableau pour stocker les objets tag
                            foreach ($data['tag'] as $tagId) {
                                $tag = $tagRepository->find($tagId);
                                //$tag = $entityManager->getRepository(tag::class)->find($tagId); // Récupérer l'tag depuis la base de données
                                if ($tag) {
                                    $tags[] = $tag; // Ajouter l'tag trouvé au tableau
                                }
                            }
                            foreach($tags as $tag)
                            {
                                $recipe->addtag($tag);
                            }
                        }


            //EDITER LA CATEGORIE
                if (isset($data['category']) && is_array($data['category'])) 
                {
                    foreach ($data['category'] as $categoryId) {
                        $category = $categoryRepository->find($categoryId);
                        //$category = $entityManager->getRepository(category::class)->find($categoryId); // Récupérer l'category depuis la base de données
                        $recipe->setCategory($category);
                        }
                }

            $entityManager->persist($recipe);

        }
        $errorFields = $validator->validate($recipe);

        if (count($errorFields) > 0 ) {

            return $this->json($errorFields, 500, ['message' => 'error']);
        }
        else
        {
            $entityManager->flush();
            return $this->json($recipe, 201, [], ['groups' => 'get_recipes']); //je retourne le code Json avec les modifcations de la recette et le code de la requête
        }
    }
//}
    /**
     * SUPPRIMER UNE RECETTE via un formulaire envoyé en JSON
     *
     * @return Response
     */
    #[Route('api/recipe/delete/{idRecipe}', name: 'api_recipe_delete', methods: ['DELETE'])]
    public function delete(RecipeRepository $recipeRepository, $idRecipe, EntityManagerInterface $entityManager, QuantityRepository $quantityRepository, CommentRepository $commentRepository)
    {
        // Je recupere la recette
        $recipe = $recipeRepository->find($idRecipe);

        //SUPPRESSION DES QUANTITÉS ASSOCIÉES A LA RECETTE

            //Je viens récupérer toutes les quantités associées à ma recette
            $quantitiesFromBdd = $quantityRepository->findByRecipeId($idRecipe);

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
            $commentsFromBdd = $commentRepository->findCommentsByRecipeId($idRecipe);
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
        $entityManager->flush(); //j'enregistre en database

        return $this->json(
            $recipe,
            200,
            ['Recette supprimée'], // je retourne un message pour informer l'utilisateur que la recette a bien été supprimée
            ['groups' => 'get_recipes']
        );
    }


}




