<?php

namespace App\Controller;

use App\Entity\Candys;
use App\Repository\CandysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, CandysRepository $candysRepository)
    {
        $panier = $session->get('panier', []);
        
        //on initialise des variables
        $data = [];
        $total = 0;

        foreach($panier as $id => $quantity){
            $candy = $candysRepository->find($id);

            $data[] = [
                'candy' => $candy,
                'quantity' => $quantity
            ];
            $total += $candy->getPrice() * $quantity;
        }
        
        return $this->render('cart/index.html.twig', compact('data', 'total'));
    }



    #[Route('/add/{id}', name: 'add')]
    public function add(Candys $candy, SessionInterface $session)
    {
        ///on récupere l'id du produit
        $id = $candy->getId();

        //on récupere le panier existant
        $panier = $session->get('panier', []);

        //on ajoute le produit dans le panier s'il n'y est pas encore
        //sinon on incrémente sa quantité
        if(empty($panier[$id])){
            $panier[$id] = 1;
        }else{
            $panier[$id]++;
        }

        $session->set('panier', $panier);
        
        //on redirige vers la page du panier
        return $this->redirectToRoute('cart_index');

    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Candys $candy, SessionInterface $session)
    {
        ///on récupere l'id du produit
        $id = $candy->getId();

        //on récupere le panier existant
        $panier = $session->get('panier', []);

        //on retire le produit du panier s'il n'y a qu'un exemplaire
        //sinon on décrémente sa quantité
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
            $panier[$id]--;
            }else{
            unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);
        
        //on redirige vers la page du panier
        return $this->redirectToRoute('cart_index');

    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Candys $candy, SessionInterface $session)
    {
        ///on récupere l'id du produit
        $id = $candy->getId();

        //on récupere le panier existant
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
        
        //on redirige vers la page du panier
        return $this->redirectToRoute('cart_index');

    }

    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('panier');
        return $this->redirectToRoute('cart_index');
    }

    
}