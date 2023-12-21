<?php

namespace App\Controller;

use App\Entity\Candys;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bonbons', name: 'candys_')]
class CandysController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('candys/index.html.twig');
    }

    #[Route('/{slug}', name:'details')]
    public function details(Candys $candy): Response
    {
        return $this->render('candys/details.html.twig', compact('candy'));
    }
}