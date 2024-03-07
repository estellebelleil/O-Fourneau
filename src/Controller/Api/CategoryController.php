<?php

namespace App\Controller\Api;


use App\Repository\CategoryRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    /**
     * Liste des catégories
     */
    #[Route('api/category/list', name: 'api_category_list', methods: ['GET'])]
    public function list(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->json(
            $categories,
            200,
            [''],
            ['groups' => 'get_categories'] 
        );
    }
}