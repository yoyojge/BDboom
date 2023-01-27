<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Collectionn;
use App\Form\CollectionnType;
use App\Entity\AlbumCollection;
use App\Repository\AlbumRepository;
use App\Repository\CollectionnRepository;
use App\Repository\AlbumCollectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/collectionn')]
class CollectionnController extends AbstractController
{
    #[Route('/', name: 'app_collectionn_index', methods: ['GET'])]
    public function index(CollectionnRepository $collectionnRepository): Response
    {
        
        //TODO: ne remonter que les collections du user connecté
        
        return $this->render('collectionn/index.html.twig', [
            'collectionns' => $collectionnRepository->findAll(),
        ]);
    }




    //ajouter une collection a un user
    #[Route('/new', name: 'app_collectionn_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CollectionnRepository $collectionnRepository): Response
    {
        $collectionn = new Collectionn();
        $form = $this->createForm(CollectionnType::class, $collectionn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            //on definit le user associé et connecté a la collection
            $user = $this->getUser();
            $collectionn ->setCollector($user);

            $collectionnRepository->save($collectionn, true);

            //ajout d'un message flash
            $this->addFlash('collectionAjout', 'Bravo, Votre collection a été ajoutée');

            return $this->redirectToRoute('app_BDboom_BDtheque', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collectionn/new.html.twig', [
            'collectionn' => $collectionn,
            'form' => $form,
        ]);
    }




    //PAGE COLLECTION detail :: liste livre dans la collection
    #[Route('/{id}', name: 'app_collectionn_show', methods: ['GET'])]
    public function show(Collectionn $collectionn, Request $request, /*AlbumCollectionRepository $albumCollectionRepository, */CollectionnRepository $collectionnRepository, AlbumRepository $albumRepository ): Response
    {        
        
        $ListeAlbumCollection = $collectionn->getAlbums();   

        
        return $this->render('collectionn/show.html.twig', [
            'collectionn' => $collectionn,
            'ListeAlbumCollection' => $ListeAlbumCollection

        ]);
    }






    #[Route('/{id}/edit', name: 'app_collectionn_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collectionn $collectionn, CollectionnRepository $collectionnRepository): Response
    {
        $form = $this->createForm(CollectionnType::class, $collectionn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collectionnRepository->save($collectionn, true);

            return $this->redirectToRoute('app_collectionn_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collectionn/edit.html.twig', [
            'collectionn' => $collectionn,
            'form' => $form,
        ]);
    }






    #[Route('/{id}', name: 'app_collectionn_delete', methods: ['POST'])]
    public function delete(Request $request, Collectionn $collectionn, CollectionnRepository $collectionnRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collectionn->getId(), $request->request->get('_token'))) {
            $collectionnRepository->remove($collectionn, true);
        }

        return $this->redirectToRoute('app_collectionn_index', [], Response::HTTP_SEE_OTHER);
    }



    
}
