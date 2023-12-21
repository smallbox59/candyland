<?php

namespace App\Controller;

use App\Entity\Candys;
use App\Entity\Categories;
use App\Repository\CandysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category, 
    CandysRepository $candysRepository, Request $request): Response
    {
        //on va chercher le numero de page dans l'url
        $page = $request->query->getInt('page', 1);

        //on va chercher la liste des produits de la catégorie
        $candys = $candysRepository->findCandysPaginated($page, $category->getSlug(), 3);

        return $this->render('categories/list.html.twig', compact('category', 'candys'));
        //synthaxe alternative
        // return $this->render('categories/list.html.twig', [
        //     'category' => $category,
        //     'candys' => $candys
        // ]);
        
    }

}