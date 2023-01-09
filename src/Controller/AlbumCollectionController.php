<?php

namespace App\Controller;

use App\Entity\AlbumCollection;
use App\Form\AlbumCollectionType;
use App\Repository\AlbumCollectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/album/collection')]
class AlbumCollectionController extends AbstractController
{
    #[Route('/', name: 'app_album_collection_index', methods: ['GET'])]
    public function index(AlbumCollectionRepository $albumCollectionRepository): Response
    {
        return $this->render('album_collection/index.html.twig', [
            'album_collections' => $albumCollectionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_album_collection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlbumCollectionRepository $albumCollectionRepository): Response
    {
        $albumCollection = new AlbumCollection();
        $form = $this->createForm(AlbumCollectionType::class, $albumCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumCollectionRepository->save($albumCollection, true);

            return $this->redirectToRoute('app_album_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('album_collection/new.html.twig', [
            'album_collection' => $albumCollection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_album_collection_show', methods: ['GET'])]
    public function show(AlbumCollection $albumCollection): Response
    {
        return $this->render('album_collection/show.html.twig', [
            'album_collection' => $albumCollection,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_album_collection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AlbumCollection $albumCollection, AlbumCollectionRepository $albumCollectionRepository): Response
    {
        $form = $this->createForm(AlbumCollectionType::class, $albumCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $albumCollectionRepository->save($albumCollection, true);

            return $this->redirectToRoute('app_album_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('album_collection/edit.html.twig', [
            'album_collection' => $albumCollection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_album_collection_delete', methods: ['POST'])]
    public function delete(Request $request, AlbumCollection $albumCollection, AlbumCollectionRepository $albumCollectionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$albumCollection->getId(), $request->request->get('_token'))) {
            $albumCollectionRepository->remove($albumCollection, true);
        }

        return $this->redirectToRoute('app_album_collection_index', [], Response::HTTP_SEE_OTHER);
    }
}
