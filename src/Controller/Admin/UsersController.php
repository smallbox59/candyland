<?php
namespace App\Controller\Admin;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateurs', name:'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->findBy([], ['firstname' => 'asc']);

        return $this->render('admin/users/index.html.twig', compact('users'));
    }

    #[Route('/upgradeRole/{id}', name:'upgrade')]
    public function upgrade(Users $user, Request $request, EntityManagerInterface $em): Response
    {

        $user->setRoles(["ROLE_ADMIN"]);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('admin_users_index');
    }
}