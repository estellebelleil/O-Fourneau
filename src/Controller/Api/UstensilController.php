<?php

namespace App\Controller\Api;

use App\Repository\UstensilRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UstensilController extends AbstractController
{
    
    #[Route('api/ustensil/list', name: 'api_ustensil_list', methods: ['GET'])]
    public function list(UstensilRepository $ustensilRepository)
    {     
        $ustensils = $ustensilRepository->findAll();
        return $this->json(
            $ustensils,
            200,
            ['Tout est okay'],
            ['groups' => 'get_ustensils'] 
        );
    }

}