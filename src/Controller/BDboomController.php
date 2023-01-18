<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Album;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\BDboomRepository;
use App\Repository\CollectionnRepository;
use App\Repository\BDboomAPIsearchRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/')]
class BDboomController extends AbstractController
{

    private $session;

    public function __construct(RequestStack $requestStack) {
        $this->session = $requestStack->getSession();
    }
    
    
    // index du site
    #[Route('/', name: 'app_BDboom_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, BDboomRepository $BDboomRepository): Response
    {
        
          
        $tabActu = $BDboomRepository->RSS('https://blog.bdboom.fr/category/actu/feed/' );
        $tabBleu = $BDboomRepository->RSS('https://blog.bdboom.fr/category/bleucomme/feed/' );
        $tabBoom = $BDboomRepository->RSS('https://blog.bdboom.fr/category/quifaitboom/feed/' );
         
        // dd($tabBleu);
        
        return $this->render('BDboom/index.html.twig', [
            'tabActu' => $tabActu,
            'tabBleu' => $tabBleu,
            'tabBoom' => $tabBoom,
        ]);
    }




    // page de resultat apres formulaire de recherche du header
    #[Route('/listeResultat', name: 'app_BDboom_listeResultat', methods: ['POST'])]
    public function listeResultat(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request): Response
    {
        $bdsearch =$request->get('bdsearch');
        // dd($bdsearch);

        //recherche avec API amazon
        $listItemsAmazon = $BDboomAPIsearchRepository->APIsearch($bdsearch);
        $this->session->set('sessSearchAmazon', $listItemsAmazon);
        
        //recherche avec API google book
        $bdsearchGGbook = str_replace(" ","+",$bdsearch);
        $urlGGbook = 'https://www.googleapis.com/books/v1/volumes?q='.$bdsearchGGbook.'&key='.$this->getParameter('app.googleapikey');
        $listDecodeGGbook = json_decode(file_get_contents($urlGGbook), true); 
        $listItemsGGbook = $listDecodeGGbook['items'];
        $this->session->set('sessSearchGGbook', $listItemsGGbook);
        // dd($urlGGbook);

       

        return $this->render('BDboom/listeResultat.html.twig', [
            'listItemsAmazon' => $listItemsAmazon,
            'listItemsGGbook' => $listItemsGGbook,
        ]);
    }





    // page detail livre
    #[Route('/detail', name: 'app_BDboom_detail', methods: ['GET'])]
    public function detail(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request): Response
    {    
 
        // - 1 decalage entre le retour de loop dans twig et l'indexation des tableau qui commence a 0
        if(!empty($request->query->get('productAmazon'))){
            $sessSearchFullAmazon = $this->session->get('sessSearchAmazon', []);
            $indexSess = $request->query->get('productAmazon') -1;
            $this->session->set('sessSearchDetailIndex', $indexSess);        
            $this->session->set('sessSearchDetail', $sessSearchFullAmazon[$indexSess]);
            $this->session->set('sessSearchFrom', 'amazon');  
        }

        if(!empty($request->query->get('productGGbook'))){
            $sessSearchFullGGbook = $this->session->get('sessSearchGGbook', []);
            $indexSess = $request->query->get('productGGbook') -1;
            $this->session->set('sessSearchDetailIndex', $indexSess);        
            $this->session->set('sessSearchDetail', $sessSearchFullGGbook[$indexSess]);
            $this->session->set('sessSearchFrom', 'ggbook'); 
        }        

        // dd($sessSearchFull[$indexSess]);
        // dd($this->session->get('sessSearchDetail'));        

        return $this->render('BDboom/detail.html.twig', [
            // 'item' => $sessSearchFull[$indexSess],
            'item' => $this->session->get('sessSearchDetail'),
            'from' => $this->session->get('sessSearchFrom'),
        ]);
    }




    // AJOUTER UN LIVRE A SA COLLECTION
    #[Route('/addItemToCollection', name: 'app_BDboom_addItemToCollection', methods: ['GET'])]
    public function addItemToCollection(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request ): Response
    {
        
        $sessSearchDetail = $this->session->get('sessSearchDetail', []);

        $sessSearchDetailIndex = $this->session->get('sessSearchDetailIndex', []);


        dd( $this->session->get('sessSearchDetail', []) );

        //on enregistre le livre dans la table livre
        $album = new Album;

        //si from GGbook
        if($this->session->get('sessSearchFrom') == "ggbook"){
            $title = $sessSearchDetail['volumeInfo']['title'];
            $album->setTitle($title);
            $description = $sessSearchDetail['volumeInfo']['description'];
            $album->setDescription($description);
        }

        //on enregistre le livre dans la collection du user
        
        // return $this->render('BDboom/detail.html.twig', [
        //     'item' => $sessSearch[$indexSess],
        // ]);

        //    TODO: on reste sur la meme page 
        // return $this->redirectToRoute('app_BDboom_detail', ['product'=>$sessSearchDetailIndex], Response::HTTP_SEE_OTHER);
        return $this->redirectToRoute('app_BDboom_listeResultat', Response::HTTP_SEE_OTHER);
    }






    // page BDtheque
    #[Route('/BDtheque', name: 'app_BDboom_BDtheque', methods: ['GET'])]
    public function BDtheque(UserRepository $userRepository, BDboomRepository $BDboomRepository,CollectionnRepository $collectionnRepository,  ): Response
    {
        
        // dd($tabBleu);
        //on recupere l utilisateur connecté
        //magie de symfony, on peut acceder a $user
        $user = $this->getUser();
        $id = $user->getId();

        //retouver toutes les collections attaché a l'id de l'user ::  marche pas !!!
        // $userCollections = $user->getCollectionns();
        // dd($userCollections);
        $collectionnArray = $collectionnRepository->findBy( ['collector' => $user] );
        
        // dd($collectionnArray);
        
        return $this->render('BDboom/BDtheque.html.twig', [
            'collectionns' => $collectionnArray,
        ]);
    }




    // test chat en scss
    #[Route('/miaou', name: 'app_BDboom_miaou', methods: ['GET'])]
    public function miaou()
    {            
        return $this->render('BDboom/miaou.html.twig', []);
    }

    #[Route('/inscription', name: 'app_BDboom_inscription', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // on met le role par defaut a user
            $user->setRoles(['ROLE_USER']);

            //on ashe le mot de passe
            $password = $passwordHasher->hashPassword($user, $request->get('user')['password']);
            $user->setPassword ($password);
            
            $userRepository->save($user, true);            

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BDboom/inscription.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    // page RCPU
    #[Route('/rcpu', name: 'app_BDboom_rcpu', methods: ['GET'])]
    public function rcpu(UserRepository $userRepository, BDboomRepository $BDboomRepository): Response
    {
        
        // dd($tabBleu);
        
        return $this->render('BDboom/rcpu.html.twig', [
            
        ]);
    }

    // page CUPU
    #[Route('/cupu', name: 'app_BDboom_cupu', methods: ['GET'])]
    public function cupu(UserRepository $userRepository, BDboomRepository $BDboomRepository): Response
    {
        
        // dd($tabBleu);
        
        return $this->render('BDboom/cupu.html.twig', [
            
        ]);
    }

    
}
