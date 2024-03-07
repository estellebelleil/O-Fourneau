<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class TagController extends AbstractController
{
    
    #[Route('api/tag/list', name: 'api_tag_list', methods: ['GET'])]
    public function list(TagRepository $TagRepository)
    {
        
        $tags = $TagRepository->findAll();
        return $this->json(
            $tags,
            200,
            [''],
            ['groups' => 'get_tags'] 
        );
    }
}