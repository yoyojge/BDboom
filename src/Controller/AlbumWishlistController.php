<?php

namespace App\Controller;

use App\Entity\AlbumWishlist;
use App\Form\AlbumWishlistType;
use App\Repository\AlbumWishlistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/album/wishlist')]
class AlbumWishlistController extends AbstractController
{
    #[Route('/', name: 'app_album_wishlist_index', methods: ['GET'])]
    public function index(AlbumWishlistRepository $albumWishlistRepository): Response
    {
        return $this->render('album_wishlist/index.html.twig', [
            'album_wishlists' => $albumWishlistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_album_wishlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlbumWishlistRepository $albumWishlistRepository): Response
    {
        $albumWishlist = new AlbumWishlist();
        $form = $this->createForm(AlbumWishlistType::class, $albumWishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumWishlistRepository->save($albumWishlist, true);

            return $this->redirectToRoute('app_album_wishlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('album_wishlist/new.html.twig', [
            'album_wishlist' => $albumWishlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_album_wishlist_show', methods: ['GET'])]
    public function show(AlbumWishlist $albumWishlist): Response
    {
        return $this->render('album_wishlist/show.html.twig', [
            'album_wishlist' => $albumWishlist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_album_wishlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AlbumWishlist $albumWishlist, AlbumWishlistRepository $albumWishlistRepository): Response
    {
        $form = $this->createForm(AlbumWishlistType::class, $albumWishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumWishlistRepository->save($albumWishlist, true);

            return $this->redirectToRoute('app_album_wishlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('album_wishlist/edit.html.twig', [
            'album_wishlist' => $albumWishlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_album_wishlist_delete', methods: ['POST'])]
    public function delete(Request $request, AlbumWishlist $albumWishlist, AlbumWishlistRepository $albumWishlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$albumWishlist->getId(), $request->request->get('_token'))) {
            $albumWishlistRepository->remove($albumWishlist, true);
        }

        return $this->redirectToRoute('app_album_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
