<?php
namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Candys;
use App\Form\CandysFormType;
use App\Repository\CandysRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/candys', name:'admin_candys_')]
class CandysController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(CandysRepository $candysRepository): Response
    {
        $candys = $candysRepository->findAll();
        return $this->render('admin/candys/index.html.twig', compact('candys'));
    }

    #[Route('/ajout', name:'add')]
    public function add(Request $request, EntityManagerInterface $em, 
    SluggerInterface $slugger, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //on crée un nouveau produit
        $candy = new Candys();

        //on crée le formulaire
        $candyForm = $this->createForm(CandysFormType::class, $candy);

        //on traite la requete du formulaire
        $candyForm->handleRequest($request);
        
        //on verifie si le formulaire est soumis et valide
        if($candyForm->isSubmitted() && $candyForm->isValid()){
            //on va récuperer les images
            $images = $candyForm->get('images')->getData();

            foreach($images as $image){
                //on définit le dossier de destination
                $folder = 'candys';

                //on appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);
                
                $img = new Images();
                $img->setName($fichier);
                $candy->addImage($img);
            }


            //on genere le slug
            $slug = $slugger->slug($candy->getName());
            
            $candy->setSlug($slug);

            //on arrondi le prix
            // $prix = $candy->getPrice() * 100;
            // $candy->setPrice($prix);

            //on stock
            $em->persist($candy);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            //on redirige
            return $this->redirectToRoute('admin_candys_index');

        }

        return $this->render('admin/candys/add.html.twig', [
            'candyForm' => $candyForm->createView()
        ]);

        //alternative synthaxe
        // return $this->renderForm('admin/candys/add.html.twig', compact('candyForm'));

        
    }

    #[Route('/edition/{id}', name:'edit')]
    public function edit(Candys $candy, Request $request, EntityManagerInterface $em, 
    SluggerInterface $slugger, PictureService $pictureService): Response
    {
        //on verifie si l'utilisateur peut editer avec le voter
        $this->denyAccessUnlessGranted('CANDY_EDIT', $candy);

        //on divise le prix par 100
        // $prix = $candy->getPrice() / 100;
        // $candy->setPrice($prix);
        //on crée le formulaire
        $candyForm = $this->createForm(CandysFormType::class, $candy);

        //on traite la requete du formulaire
        $candyForm->handleRequest($request);
        
        //on verifie si le formulaire est soumis et valide
        if($candyForm->isSubmitted() && $candyForm->isValid()){
            //on va récuperer les images
            $images = $candyForm->get('images')->getData();

            foreach($images as $image){
                //on définit le dossier de destination
                $folder = 'candys';

                //on appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);
                
                $img = new Images();
                $img->setName($fichier);
                $candy->addImage($img);
            }
            
            //on genere le slug
            $slug = $slugger->slug($candy->getName());
            
            $candy->setSlug($slug);

            //on arrondi le prix
            // $prix = $candy->getPrice() * 100;
            // $candy->setPrice($prix);

            //on stock
            $em->persist($candy);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');

            //on redirige
            return $this->redirectToRoute('admin_candys_index');

        }

        return $this->render('admin/candys/edit.html.twig', [
            'candyForm' => $candyForm->createView(),
            'candy' => $candy
        ]);
    }

    #[Route('/suppression/{id}', name:'delete')]
    public function delete(Candys $candy): Response
    {
        //on verifie si l'utilisateur peut supprimer avec le voter
        $this->denyAccessUnlessGranted('CANDY_DELETE', $candy);

        return $this->render('admin/candys/index.html.twig');
    }

    #[Route('/suppression/image/{id}', name:'delete_image', methods: ['DELETE'])]
    public function deleteImage(Images $image, Request $request, 
    EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {
        //on recupere le contenu de la requete
        $data = json_decode($request->getContent(), true);
        
        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
            //le token csrf est valide
            //on recupere le nom de l'image
            $nom = $image->getName();

            if($pictureService->delete($nom, 'candys', 300, 300)){
                //on supprime l'image de la bdd
                $em->remove($image);
                $em->flush();

                return new JsonResponse(['success' => true], 200);
            }
            //la suppression a échoué
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}