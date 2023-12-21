<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\CandysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commandes', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, CandysRepository $candysRepository, 
    EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);
        
        if($panier === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('main');
        }

        //le panier n'est pas vide, on crée la commande
        $order = new Orders();

        //on remplit la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        //on parcout le panier pour créer les détails de commande
        foreach($panier as $item => $quantity){
            $orderDetails = new OrdersDetails();

            //on va chercher le produit
            $candy = $candysRepository->find($item);

            $price = $candy->getPrice();

            //on crée le détails de commande
            $orderDetails->setCandys($candy);
            $orderDetails->setPrice($price);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
            
        }

        //on persiste et on flush
        $em->persist($order);
        $em->flush();

        $session->remove('panier');

        $this->addFlash('message', 'commande créée avec succès');
        return $this->redirectToRoute('main');

        // return $this->render('orders/index.html.twig', [
        //     'controller_name' => 'OrdersController',
        // ]);
    }
}
