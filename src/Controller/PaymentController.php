<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Users;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{

    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }



    #[Route('/payment/{id}', name: 'app_payment')]
    public function payment(UsersRepository $usersRepository, OrdersRepository $ordersRepository, 
    OrdersDetailsRepository $ordersDetailsRepository, Orders $orders, EntityManagerInterface $em): RedirectResponse
    {
        
            $total = 0;
        
            $orderDetails = $orders->getOrdersDetails();
            foreach($orderDetails as $orderDetail){
                $price = $orderDetail->getPrice();
                $quantity = $orderDetail->getQuantity();
                $total += $price * $quantity;
                
            }

            $prix[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $total,
                    'product_data' => [
                        'name' => $orders->getReference()
                    ]
                ],
                'quantity' => 1,
                ];
        
            Stripe::setApiKey( apiKey: 'sk_test_51OUpaGLA57IEBc43Mdhk1vlR3SN1RM9pwgJrsbKFHKDfj8CCpCSihxWemj5jyNymN4OKeqAq8RheNWJpxIIyxDay00jfmSmycd');

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                  # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                  $prix
                ]],
                'mode' => 'payment',
                'success_url' => $this->generator->generate(name: 'stripe_success', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generator->generate(name: 'stripe_faillure', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
              ]);
            $em->remove($orders); 
            $em->flush();     
        
            return new RedirectResponse($checkout_session->url);
        
    }

    // $user = $this->getUser();

    #[Route('/stripesuccess', name:'stripe_success')]
    public function stripeSuccess()
    {
        $this->addFlash('success', 'paiment effectué avec succès');
        return $this->redirectToRoute('main');
    }

    #[Route('/stripefaillure', name:'stripe_faillure')]
    public function stripeFaillure()
    {
        $this->addFlash('danger', 'paiment échoué');
        return $this->redirectToRoute('main');
    }

    #[Route('/index', name: 'index')]
    public function index(UsersRepository $usersRepository, OrdersRepository $ordersRepository, 
    OrdersDetailsRepository $ordersDetailsRepository): Response
    {
        $user = $this->getUser();
        $orders = $ordersRepository->findBy(array('users' => $user));
        
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
            'orders' => $orders,
        ]);
    }
}


// $total = 0;
        
        // foreach($orders as $order){
            
        // $orderDetails = $order->getOrdersDetails();
            
        // }
// foreach($orderDetails as $orderDetail){
            //     $price = $orderDetail->getPrice();
            //     $quantity = $orderDetail->getQuantity();
            //     $total += $price * $quantity;
                
            // }