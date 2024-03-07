<?php

namespace App\Controller\Api;

use App\Entity\Tip;
use App\Repository\TipRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class TipController extends AbstractController
{
    
    #[Route('api/tip/list', name: 'api_tip_list', methods: ['GET'])]
    public function list(TipRepository $TipRepository)
    {
        
        $tips = $TipRepository->findAll();

        return $this->json(
            $tips,
            200,
            [''],
            ['groups' => 'get_tips'] 
        );
    }

    #[Route('api/tip/show/{id}', name: 'api_tip_show', methods: ['GET'])]
    public function show (TipRepository $TipRepository,$id)
    {
        
        $tips = $TipRepository->find($id);
        return $this->json(
            $tips,
            200,
            [''],
            ['groups' => 'get_tips'] 
        );
    }
    /**
     * RECUPERE UNE ASTUCE ALEATOIRE
     *
     * @return Response
     */
    #[Route('api/tip/random', name: 'api_tip_random', methods: ['GET'])]
    public function randomTip(TipRepository $tipRepository)
    {
        $randomtip = $tipRepository->getRandomTip(); // je recupere l'id de la recette

        return $this->json(
            $randomtip,
            200,
            [],
            ['groups' => 'get_tips']
        );
    }

}