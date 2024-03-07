<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CommentController extends AbstractController
{

    /**
     * Liste tous les commentaires
     */
    #[Route('api/comment/list', name: 'api_comment_list', methods: ['GET'])]
    public function list(CommentRepository $commentRepository)
    {
        
        $comments = $commentRepository->findAll();
        
        return $this->json(
            $comments,
            200,
            [''],
            ['groups' => 'get_comments'] 
        );
    }

    /**
     * Liste tous les commentaires en fonction de la recette donnée
     */
    #[Route('api/comment/list/{idRecipe}', name: 'api_commentsByRecipe_list', methods: ['GET'])]
    public function commentsByRecipe(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, $idRecipe, RecipeRepository $recipeRepository)
    {
    $user = $this->getUser();
    $recipe = $recipeRepository->find($idRecipe);

    return $this->json(
        $recipe, 
        201, 
        [], 
        ['groups' => 'get_comments']); 
    }

    /**
     * Affiche un commentaire
     */
    #[Route('api/comment/show/{id}', name: 'api_comment_show', methods: ['GET'])]
    public function show(CommentRepository $commentRepository, $id)
    {
        $comment = $commentRepository->find($id); 
        
        return $this->json(
            $comment,
            200,
            [],
            ['groups' => 'get_comments','get_users']
        );
    }

    /**
     * Créer un commentaire en fonction de la recette donnée
     */
    #[Route('api/comment/add/{idRecipe}', name: 'api_comment_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, $idRecipe, RecipeRepository $recipeRepository): Response
    {
        $content = $request->getContent(); // je récupère le contenu de la requête
        $data = json_decode($content, true); // je décode le contenu JSON en tableau associatif
        $user = $this->getUser();
        $recipe = $recipeRepository->find($idRecipe);

        $comment = new Comment;
        $comment->setUser($user);
        $comment->setContent($data['content']);
        $comment->setRate($data['rate']);
        $comment->setRecipe($recipe);
        $comment->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json(
            $comment, 
            201, 
            [], 
            ['groups' => 'get_comments']); 
    }

    /**
     * Modifie un commentaire en fonction de l'id du commentaire
     */
    #[Route('api/comment/edit/{idComment}', name: 'api_comment_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer,$idComment,CommentRepository $commentRepository): Response
    {
        $comment = $commentRepository->find($idComment); // je recupere l'id du commentaire
        $user = $this->getUser();
        //dd($user);
        $data = json_decode($request->getContent(), true);// je recupere le contenu de la requete en json afin de le transformer en tableau et de le stocker dans $data
        
        // on compare avec l'auteur du commentaire
        if ($user !== $comment->getUser() && !$this->isGranted('ROLE_ADMIN')) 
        {
            throw $this->createAccessDeniedException('Non autorisé.');
        }
        else
        {
            $comment->setContent($data['content']); // je modifie le contenu du commentaire grace a la methode setContent
            $comment->setRate($data['rate']); // je modifie le rate du commentaire grace a la methode setRate
            $comment->setUpdatedAt((new \DateTimeImmutable()));// je modifie la date de mise a jour du commentaire grace a la methode setUpdatedAt

            $entityManager->flush();// j'enregistre en database
        }


        return $this->json($comment, 201, [], ['groups' => 'get_comments']);
    }
    
    /**
     * Suppression du commentaire
     */
    #[Route('api/comment/delete/{idComment}', name: 'api_comment_delete', methods: ['DELETE'])]
    public function delete(CommentRepository $commentRepository, $idComment, EntityManagerInterface $entityManager)
    {
        $comment = $commentRepository->find($idComment);
        $user = $this->getUser();
        // on compare avec l'auteur du commentaire
        if ($user !== $comment->getUser() && !$this->isGranted('ROLE_ADMIN')) 
        {
            throw $this->createAccessDeniedException('Non autorisé.');
        }
        else
        {
        $entityManager->remove($comment); 
        $entityManager->flush();
        }
        return $this->json(
            $comment,
            200,
            ['Le commentaire a bien été supprimée'], 
            ['groups' => 'get_comments']
        );
    }
}