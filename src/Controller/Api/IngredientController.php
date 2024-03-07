<?php

namespace App\Controller\Api;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class IngredientController extends AbstractController
{
    
    #[Route('api/ingredient/list', name: 'api_ingredient_list', methods: ['GET'])]
    public function list(IngredientRepository $ingredientRepository)
    {
        $ingredient = $ingredientRepository->findAll();
        return $this->json(
            $ingredient,
            200,
            ['Tout est okay'],
            ['groups' => 'get_ingredients'] 
        );
    }
}