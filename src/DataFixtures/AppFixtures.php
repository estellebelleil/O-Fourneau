<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\OfourneauProvider;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\Tip;
use App\Entity\User;
use App\Entity\Ustensil;
use App\Service\MySlugger;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $slugger;

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        
        // Je vais initialiser faker en créant une instance de Faker
        $faker = Factory::create('fr_FR');
        // Je créer une instance du provider OfourneauProvider
        $provider = new OfourneauProvider();
        
        
        //CREER LES CATEGORIES
            $listCategories = [];
            $categoriesArray = $provider->categories();
            foreach ($categoriesArray as $category) {
                $categories = new Category();
                $categories->setName($category);
                $listCategories [] = $categories;
                $manager->persist($categories);
            };

        //CREER LES USTENSILES
            $listUstensil = [];
            $ustensilsArray = $provider->ustensils();
            foreach ($ustensilsArray as $ustensil) {
                $ustensils = new Ustensil();
                $ustensils->setName($ustensil);
                $listUstensil [] = $ustensils;
                $manager->persist($ustensils);
            };
        
        //CREER LES TAGS
            $listTag = [];
            $tagsArray = $provider->tags();
            foreach ($tagsArray as $tag) {
                $tags = new Tag();
                $tags->setName($tag);
                $listTag [] = $tags;
                $manager->persist($tags);
            }
            //CREER LES TIPS
            $listTip = [];
            $tipsArray = $provider->tips();
            foreach ($tipsArray as $tipName) {
                $tip = new Tip();
                $tip->setTitle($tipName);
                $tip->setContent($faker->text(500));
                $tip->setPicture($faker->imageUrl(300, 480, $tip->getTitle(), true));
                $tip->setSlug($this->slugger->slugify($tip->getTitle()));
                $listTip [] = $tip;
                $manager->persist($tip);
            }
            
        //CREER DES UTILISATEURS
            // 1er : utilisateur admin
            $user = new User(); // On créer l'user
            $user->setEmail("admin@admin.fr"); // On lui donne un email
            $user->setName('Admin');
            $user->setRoles(['ROLE_ADMIN']); // On donne le role admin a cet user
            $user->setPassword(password_hash("ofourneau",PASSWORD_BCRYPT));
            $manager->persist($user); // On persist

            // 2eme : utilisateur manager
            $user = new User(); // On créer l'user
            $user->setEmail("manager@manager.fr"); // On lui donne un email
            $user->setName('Manager');
            $user->setRoles(['ROLE_MANAGER']); // On donne le role manager a cet user
            $user->setPassword(password_hash("ofourneau",PASSWORD_BCRYPT));
            $manager->persist($user); // On persist

            $listUser = [];
            // 3eme : utilisateur user (classique)
            for ($i = 0; $i < 5; $i++) 
            { 
                $rand = rand(10,99);
                $user = new User(); // On créer l'user
                $user->setEmail($faker->email()); // On lui donne un email
                $user->setName($faker->firstName().'du'. $rand);
                $user->setRoles(['ROLE_USER']); // On donne le role user a cet user
                $user->setPassword(password_hash("ofourneau",PASSWORD_BCRYPT));
                $listUser[] = $user;
                $manager->persist($user); // On persist
            }

        //CREER DES RECETTES
            $recipe = new Recipe();
                // Ci dessous je genere un nom de recette depuis mon tableau recipesName
            $recipesNameArray = $provider->recipesNames();
            foreach ($recipesNameArray as $recipeName)
            {
                $recipe = new Recipe();
                $recipe->setName($recipeName);

                //Ici je veux rajouter des descriptions à chacune de mes recettes
                $recipe->setDescription($faker->text(200));
                //Ici je veux créer les slugs associées aux noms des recettes
                $recipe->setSlug($this->slugger->slugify($recipe->getName()));
                //Ici je veux rajouter des descriptions à chacune de mes recettes
                $recipe->setStep('Etape 1 : '.$faker->text(120) . '. Etape 2 : '.$faker->text(200) . '. Etape 3 : '.$faker->text(150) . '. Etape 4 : '.$faker->text(175));
                    //Ici je veux rajouter des urls à mes recettes
                $recipe->setPicture($faker->imageUrl(300, 480, $recipe->getName(), true));
                //Ici je veux rajouter une date de création
                $recipe->setCreatedAt(new DateTimeImmutable());
                //Ici je veux rajouter une categorie au hasard depuis mon tableau créer plus haut catégorie
                $recipe->setCategory($listCategories[rand(0,2)]);
                $recipe->setUser($listUser[rand(0,4)]);
                //Ajouter des ustentils
                $recipe->addUstensil($listUstensil[rand(0,5)]);
                $recipe->addUstensil($listUstensil[rand(6,12)]);
                $recipe->addUstensil($listUstensil[rand(13,17)]);
                $recipe->addUstensil($listUstensil[rand(18,21)]);
                //Ajouter des tags
                $recipe->addTag($listTag[rand(0,4)]);
                $recipe->addTag($listTag[rand(5,9)]);
                $recipe->addTag($listTag[rand(10,13)]);

                $manager->persist($recipe);

                // CREER DES INGREDIENTS
                $addedIngredients = [];

                // Ajouter des ingrédients et des quantités à la recette
                $ingredientsArray = $provider->ingredients();
                shuffle($ingredientsArray);
                for ($i = 0; $i < 3; $i++) {
                    $ingredientName = $ingredientsArray[$i];

                    if (!in_array($ingredientName, $addedIngredients)) {
                        $unit = $provider->unit_rand();
                        $ingredient = new Ingredient();
                        $ingredient->setName($ingredientName);
                        $ingredient->setUnit($unit);
                        $manager->persist($ingredient);
                        
                        $quantity = new Quantity();
                        $quantity->setIngredient($ingredient);
                        $quantity->setRecipe($recipe);
                        $quantity->setQuantity(mt_rand(5, 650)); // Quantité aléatoire
                        $manager->persist($quantity);
                        
                        $addedIngredients[] = $ingredientName;
                    }
                }

                //Ajouter des commentaires
                $listComment = [];
                for ($i = 0; $i < 3; $i++)  {
                $comment = new Comment(); // Je créer une instance de l'entité Comment
                $comment->setContent($faker->text(200)); // Je définis une note
                $comment->setRate(rand(0,5));
                $comment->setCreatedAt(new DateTimeImmutable());
                $comment->setUser($listUser[rand(0,4)]);
                $comment->setRecipe($recipe);
                // Vérifier si l'utilisateur a déjà commenté cette recette
                $existingComments = $recipe->getComments();
                $userCommented = false;
                    foreach ($existingComments as $existingComment) {
                        if ($existingComment->getUser() === $user) {
                            $userCommented = true;
                            break;
                        }
                    }

                    if (!$userCommented) {
                        $listComment[] = $comment;
                        $manager->persist($comment);
                    }
                }

        $manager->flush();

        }
    }
}