<?php

namespace App\Controller\Api;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SearchBarController extends AbstractController
{

    #[Route('/api/search/{keyword}', name: 'api_search', methods: ['GET'])]
    public function findByWord($keyword, RecipeRepository $recipeRepository)
    {
        $recipesBySearch =  $recipeRepository->findBySearch($keyword);

        return $this->json(
            $recipesBySearch,
            200,
            [''],
            ['groups' => 'get_recipes'] // je veux que le groupe get_recipes soit utilisé
        );
    }
}
