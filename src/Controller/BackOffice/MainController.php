<?php

namespace App\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/back', name: 'main_back')]
    public function home (): Response
    {
    return $this->render('BackOffice/main/home.html.twig');
    }
}
