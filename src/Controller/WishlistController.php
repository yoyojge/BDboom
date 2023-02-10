<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\WishlistType;
use App\Repository\WishlistRepository;
use App\Repository\CollectionnRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/wishlist')]
class WishlistController extends AbstractController
{
    #[Route('/', name: 'app_wishlist_index', methods: ['GET'])]
    public function index(WishlistRepository $wishlistRepository): Response
    {
        return $this->render('wishlist/index.html.twig', [
            'wishlists' => $wishlistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_wishlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, WishlistRepository $wishlistRepository): Response
    {
        $wishlist = new Wishlist();
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wishlistRepository->save($wishlist, true);

            return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('wishlist/new.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form,
        ]);
    }






    //PAGE DETAIL WISHLIST :: liste des albums de la wishlist
    #[Route('/{id}', name: 'app_wishlist_show', methods: ['GET'])]
    public function show(Wishlist $wishlist, CollectionnRepository $collectionnRepository): Response
    {
        
        $ListeAlbumWishlist = $wishlist->getAlbum();   

        $user = $this->getUser();  
        $collectionns = $collectionnRepository->findBy( array('collector' => $user ) );

        // dd($ListeAlbumWishlist);
        return $this->render('wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'ListeAlbumWishlist' => $ListeAlbumWishlist,
            'collectionns' => $collectionns,
        ]);
    }







    #[Route('/{id}/edit', name: 'app_wishlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wishlist $wishlist, WishlistRepository $wishlistRepository): Response
    {
        
        
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {

            // dd('coucou');
            $wishlistRepository->save($wishlist, true);

            return $this->redirectToRoute('app_wishlist_show', [
                'id' => $wishlist->getId(),
            ], 
            Response::HTTP_SEE_OTHER);
        }
        // dd($form);
        return $this->renderForm('wishlist/edit.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_wishlist_delete', methods: ['POST'])]
    public function delete(Request $request, Wishlist $wishlist, WishlistRepository $wishlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wishlist->getId(), $request->request->get('_token'))) {
            $wishlistRepository->remove($wishlist, true);
        }

        return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
