<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Album;
use App\Form\UserType;
use App\Entity\Wishlist;
use App\Entity\Collectionn;
use App\Entity\AlbumWishlist;
use App\Entity\AlbumCollection;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use App\Repository\BDboomRepository;
use App\Repository\WishlistRepository;

use App\Repository\CollectionnRepository;
use App\Repository\AlbumWishlistRepository;
use App\Repository\AlbumCollectionRepository;
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
    





    
    // HOME :: index du site
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






    // PAGE LISTE :: de resultat apres formulaire de recherche du header
    #[Route('/listeResultat', name: 'app_BDboom_listeResultat', methods: ['POST'])]
    public function listeResultat(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request): Response
    {
        
        //recuperation de la requete de recherche et enregistrement dans une session
        $bdsearch =$request->get('bdsearch');
        $this->session->set('sessKeywordSearch', $bdsearch);

        //TODO:recherche dans BDboom



        //recherche avec API amazon        
        $listItemsAmazon = $BDboomAPIsearchRepository->APIsearch($bdsearch);
        // dd($listItemsAmazon);
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








    // PAGE DETAIL :: livre
    #[Route('/detail/{product}/{id}', name: 'app_BDboom_detail', methods: ['GET'])]
    public function detail(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request, BDboomRepository $BDboomRepository, CollectionnRepository $collectionnRepository): Response
    {    
        
        //recuperation des elements de la route
        $routeParams = $request->attributes->get('_route_params');
        $urlProduct = $routeParams['product'];
        $urlProductId = $routeParams['id'];


        //TODO: on vient de BDboom

        // on vient de amazon :: productAmazon
        if(!empty($urlProduct) && $urlProduct == "productAmazon"){
            $sessSearchFullAmazon = $this->session->get('sessSearchAmazon', []);
            // decalage - 1 entre le retour de loop dans twig et l'indexation des tableaux qui commence a 0
            $indexSess = $urlProductId -1;
            $this->session->set('sessSearchDetailIndex', $indexSess);        
            $this->session->set('sessSearchDetail', $sessSearchFullAmazon[$indexSess]);
            $this->session->set('sessSearchFrom', 'amazon');  
        }

        //on vient de ggbook :: productGGbook
        if( !empty($urlProduct) && $urlProduct == "productGGbook"){
            $sessSearchFullGGbook = $this->session->get('sessSearchGGbook', []);
            // decalage - 1 entre le retour de loop dans twig et l'indexation des tableaux qui commence a 0
            $indexSess = $urlProductId -1;
            $this->session->set('sessSearchDetailIndex', $indexSess);        
            $this->session->set('sessSearchDetail', $sessSearchFullGGbook[$indexSess]);
            $this->session->set('sessSearchFrom', 'ggbook'); 
        }        

        // dd($sessSearchFull[$indexSess]);
        // dd($this->session->get('sessSearchDetail'));        

        //on recupere les collections du user coonecte
        $user = $this->getUser();
        //ca marche pas ...
        // $collectionsUser = $user->getCollectionns();
        
        $collectionns = $collectionnRepository->findBy( array('collector' => $user ) );
        // dd($collectionns);


        return $this->render('BDboom/detail.html.twig', [
            // 'item' => $sessSearchFull[$indexSess],
            'item' => $this->session->get('sessSearchDetail'),
            'from' => $this->session->get('sessSearchFrom'),
            'collectionns' => $collectionns,
        ]);
    }







    // AJOUTER UN LIVRE A SA COLLECTION
    #[Route('/addItemToCollection', name: 'app_BDboom_addItemToCollection', methods: ['GET', 'POST'])]
    public function addItemToCollection(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request, AlbumRepository $albumRepository, BDboomRepository $BDboomRepository, AlbumCollectionRepository $albumCollectionRepository, CollectionnRepository $collectionnRepository): Response
    {
         
        //recuperation des infos de recherche stockées en session
        $sessSearchDetail = $this->session->get('sessSearchDetail', []);
        // dd($sessSearchDetail);
        $sessSearchDetailIndex = $this->session->get('sessSearchDetailIndex', []);   
        $sessSearchFrom = $this->session->get('sessSearchFrom');

        //fonction de gestion des infos du livres a inserer
        $arrayBookInfo = $BDboomRepository->bookInfo($sessSearchDetail, $sessSearchFrom);

        //date
        $Now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
       
        
        //on verifie si le livre n'est pas deja en BDD ::  (recherche par isbn)
        $trouve = $albumRepository->findBy( array('isbn' => $arrayBookInfo['isbn'] ));
        // dd($isbn, $trouve);       
        
        //si le livre n'est pas en BDD, on enregistre le livre dans la table livre
        if(empty($trouve)){

            //on enregistre l'image sur le serveur ::  que si pas deja existant
            $newPathCover = $BDboomRepository->imageLoad($arrayBookInfo['coverOnline']);

            $album = new Album;
            $album->setTitle($arrayBookInfo['title']);
            $album->setCover($newPathCover);
            $album->setDescription($arrayBookInfo['description']);
            $album->setRefBDfugues($arrayBookInfo['refBDfugue']);
            $album->setRefAmazon($arrayBookInfo['refAmazone']);
            $album->setIsbn($arrayBookInfo['isbn']);
            $album->setKeyword( $this->session->get('sessKeywordSearch')." ".$arrayBookInfo['title'] );
            $album->setAuthor($arrayBookInfo['authors']);
            $album->setBDboomDate($Now);
            $album->setOrigine( $this->session->get('sessSearchFrom') );

            $albumRepository->save($album, true);   
            
            //recuperer l'id du nouveau livre
            $albumID = $album->getId();         

        }
        else{
             //recuperer l'id du livre deja en BDD
            $albumID = $trouve[0]->getId();
            // dd($trouve[0]->getId()); 
        }             

        //on enregistre le livre dans la collection du user
        //recuperer l'id de la collection
        
        $collectionnIdSelected = $request->request->get('collectionn');

        $albumObj = $albumRepository->findOneBy( array('id' => $albumID ));
       
        // $albumObj = $albumArrayObj[0];
        $collectionObj = $collectionnRepository->findOneBy( array('id' => $collectionnIdSelected ));
        // $collectionObj = $collectionArrayObj[0];
        // $collectionObj->addAlbum($albumObj);

        $albumObj->addCollectionn($collectionObj);
        // dd($albumObj);
        $albumRepository->save($albumObj, true);

        //ajout d'un message flash
        $this->addFlash('albumAjout', 'Bravo, l album a été ajoutée à votre collection');

        //on reste sur la meme page 
        $previousUrl = $_SERVER['HTTP_REFERER'];
        $splitUrl = explode("detail/", $previousUrl);
        $myUrlParam = explode("/", $splitUrl[1]);
        // dd($myUrlParam);
        return $this->redirectToRoute('app_BDboom_detail', ['product'=>$myUrlParam[0] , 'id' => $myUrlParam[1] ], Response::HTTP_SEE_OTHER);
        
    }







    // AJOUTER UN LIVRE A SA WISHLIST
    #[Route('/addItemToWishlit', name: 'app_BDboom_addItemToWishlist', methods: ['GET', 'POST'])]
    public function addItemToWishlist(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request, AlbumRepository $albumRepository, BDboomRepository $BDboomRepository, AlbumCollectionRepository $albumCollectionRepository, CollectionnRepository $collectionnRepository, WishlistRepository $wishlistRepository, AlbumWishlistRepository $albumWishlistRepository): Response
    {
         
        //recuperation des infos de recherche stockées en session
        $sessSearchDetail = $this->session->get('sessSearchDetail', []);
        $sessSearchFrom = $this->session->get('sessSearchFrom');

        //fonction de gestion des infos du livres a inserer
        $arrayBookInfo = $BDboomRepository->bookInfo($sessSearchDetail, $sessSearchFrom);

         //date
         $Now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
       
        
         //on verifie si le livre n'est pas deja en BDD ::  (recherche par isbn)
         $trouve = $albumRepository->findBy( array('isbn' => $arrayBookInfo['isbn'] ));
         // dd($isbn, $trouve);       
         
         //si le livre n'est pas en BDD, on enregistre le livre dans la table livre
         if(empty($trouve)){
 
             //on enregistre l'image sur le serveur ::  que si pas deja existant
             $newPathCover = $BDboomRepository->imageLoad($arrayBookInfo['coverOnline']);
 
             $album = new Album;
             $album->setTitle($arrayBookInfo['title']);
             $album->setCover($newPathCover);
             $album->setDescription($arrayBookInfo['description']);
             $album->setRefBDfugues($arrayBookInfo['refBDfugue']);
             $album->setRefAmazon($arrayBookInfo['refAmazone']);
             $album->setIsbn($arrayBookInfo['isbn']);
             $album->setKeyword( $this->session->get('sessKeywordSearch')." ".$arrayBookInfo['title'] );
             $album->setAuthor($arrayBookInfo['authors']);
             $album->setBDboomDate($Now);
             $album->setOrigine( $this->session->get('sessSearchFrom') );
 
             $albumRepository->save($album, true);   
             
             //recuperer l'id du nouveau livre
             $albumID = $album->getId();
             // dd($album->getId());           
 
         }
         else{
              //recuperer l'id du livre deja en BDD
             $albumID = $trouve[0]->getId();
             // dd($trouve[0]->getId()); 
         }


        //on retrouve l id de la wishlist du user connecte
        $wishlist = new Wishlist;
        $user = $this->getUser();        
        $wishlistObj = $wishlistRepository->findOneBy( ['collector' =>  $user ]);
        // dd($wishlistObj);


        //on enregistre le livre dans la wishlist du user
        
        // $albumWishlist = new AlbumWishlist;          

        $albumObj = $albumRepository->findOneBy( array('id' => $albumID ));
        // $albumWishlist->setAlbum( $albumObj);


        $albumObj->addWishlist($wishlistObj);
        // dd($albumObj);
        $albumRepository->save($albumObj, true);

        // $collectionObj = $collectionnRepository->findBy( array('id' => $collectionnIdSelected ));
        // $albumWishlist->setWishlist($wishlistObj[0]);
        
        // $albumWishlistRepository->save($albumWishlist, true);  

        //ajout d'un message flash
        $this->addFlash('albumAjout', 'Bravo, l album a été ajoutée à votre wishlist');

        //on reste sur la meme page 
        $previousUrl = $_SERVER['HTTP_REFERER'];
        $splitUrl = explode("detail/", $previousUrl);
        $myUrlParam = explode("/", $splitUrl[1]);
        // dd($myUrlParam);
        return $this->redirectToRoute('app_BDboom_detail', ['product'=>$myUrlParam[0] , 'id' => $myUrlParam[1] ], Response::HTTP_SEE_OTHER);
        
    }






    // PAGE BDtheque
    #[Route('/BDtheque', name: 'app_BDboom_BDtheque', methods: ['GET'])]
    public function BDtheque(UserRepository $userRepository, BDboomRepository $BDboomRepository,CollectionnRepository $collectionnRepository,  ): Response
    {
        //on recupere l utilisateur connecté
        //magie de symfony, on peut acceder a $user
        $user = $this->getUser();
        // $id = $user->getId();

        //retouver toutes les collections attachées a l'id de l'user :: ca foncrionne , mais pas visible dans un dd
        $collectionnArray = $user->getCollectionns();
        $wishlistArray = $user->getWishlists();
        // dd($userCollections);
        // $collectionnArray = $collectionnRepository->findBy( ['collector' => $user] );
        
        // dd($collectionnArray);
        
        return $this->render('BDboom/BDtheque.html.twig', [
            'collectionns' => $collectionnArray,
            'wishlists' => $wishlistArray,
        ]);
    }






    






    // TODO: PAGE 404 :: test chat en scss pour la 404
    #[Route('/miaou', name: 'app_BDboom_miaou', methods: ['GET'])]
    public function miaou()
    {            
        return $this->render('BDboom/miaou.html.twig', []);
    }





    //inscription
    #[Route('/inscription', name: 'app_BDboom_inscription', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository,  UserPasswordHasherInterface $passwordHasher, CollectionnRepository $collectionnRepository, WishlistRepository $wishlistRepository): Response
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
            
            //creer et associer une collection
            //on definit le user associé et connecté a la collection
            $collectionn = new Collectionn();
            $idNewUser = $user->getId ();
            $userObj = $userRepository->findBy( array('id' => $idNewUser ));
            $collectionn->setCollector($userObj[0]);
            $collectionn->setCollectionName("Ma collection");
            $collectionnRepository->save($collectionn, true);

            //TODO: creer et associer une wishlist
            $wishlist = new Wishlist();
            $wishlist->setCollector($userObj[0]);
            $wishlist->setWishlistName("Ma wishlist");
            $wishlistRepository->save($wishlist, true);



            //TODO: ajouter la gestion de la creation de compte par mail de conf + token

            //ajout d'un message flash
            $this->addFlash('compteAjout', 'Bravo, votre compte a été correctement créé');

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('BDboom/inscription.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }







    // PAGE RCPU
    #[Route('/rcpu', name: 'app_BDboom_rcpu', methods: ['GET'])]
    public function rcpu(UserRepository $userRepository, BDboomRepository $BDboomRepository): Response
    {      
        return $this->render('BDboom/rcpu.html.twig', [ ]);
    }

    // PAGE CUPU
    #[Route('/cupu', name: 'app_BDboom_cupu', methods: ['GET'])]
    public function cupu(UserRepository $userRepository, BDboomRepository $BDboomRepository): Response
    {
        return $this->render('BDboom/cupu.html.twig', [  ]);
    }

    
}
