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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    #[Route('/listeResultat', name: 'app_BDboom_listeResultat', methods: ['GET','POST'])]
    public function listeResultat(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request, AlbumRepository $albumRepository, BDboomRepository $BDboomRepository, CollectionnRepository $collectionnRepository): Response
    {        


        if(!empty( $this->session->get('listItemsBDboom', [])  )){
            //si on vient de ajouter a la collection depuis la page liste
            $listItemsBDboom = $this->session->get('listItemsBDboom', []);
            $this->session->set('listItemsBDboom', ''); 

            $listItemsAmazon = $this->session->get('listItemsAmazon', []);
            $this->session->set('listItemsAmazon', ''); 

            $listItemsGGbook = $this->session->get('listItemsGGbook', []);
            $this->session->set('listItemsGGbook', ''); 

            $bdsearch =$this->session->get('bdsearch', []);
            $this->session->set('bdsearch', ''); 
        }  
        else{
            //depuis la page detail
            //recuperation de la requete de recherche et enregistrement dans une session
            $bdsearch =$request->get('bdsearch');
            // $this->session->set('sessKeywordSearch', $bdsearch);

            //TODO:recherche dans BDboom
            $arrayOfObjectsBDboom = $albumRepository->findByKeyword($bdsearch);        
            $listItemsBDboom = $BDboomRepository->objectToArray($arrayOfObjectsBDboom);
            // dd($listItemsBDboom);

            //recherche avec API amazon        
            $listItemsAmazonBrut = $BDboomAPIsearchRepository->APIsearchAmazon($bdsearch);
            $listItemsAmazon = $BDboomAPIsearchRepository->APIcleanAmazonData($listItemsAmazonBrut);
            // $this->session->set('sessSearchAmazon', $listItemsAmazon);
            // dd($listItemsAmazon);
            
            //recherche avec API google book
            $listItemsGGbookBrut = $BDboomAPIsearchRepository->APIsearchGoogle($bdsearch);  
            $listItemsGGbook = $BDboomAPIsearchRepository->APIcleanGoogle($listItemsGGbookBrut);  
            // $this->session->set('sessSearchGGbook', $listItemsGGbook);
            // dd($listItemsGGbook);     
        }  

        //on recupere les collections du user coonecte
        $user = $this->getUser();
        $collectionns = $collectionnRepository->findBy( array('collector' => $user ) );

        return $this->render('BDboom/listeResultat.html.twig', [
            'listItemsAmazon' => $listItemsAmazon,
            'listItemsGGbook' => $listItemsGGbook,
            'listItemsBDboom' => $listItemsBDboom,
            'bdsearch' => $bdsearch,
            'collectionns' => $collectionns,
        ]);
    }








    // PAGE DETAIL :: livre
    #[Route('/detail/{product}/{id}', name: 'app_BDboom_detail', methods: ['GET','POST'])]
    public function detail(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request, BDboomRepository $BDboomRepository, CollectionnRepository $collectionnRepository): Response
    {            
        //recuperation des elements de la route
        // $routeParams = $request->attributes->get('_route_params');
        // $urlProduct = $routeParams['product'];
        // $urlProductId = $routeParams['id'];

        $bdsearch = $request->request->get('bdsearch');
            
        //recuperation des infos par session ou par json si on vient de addCollection OU addWishlist
        if(!empty( $this->session->get('bookDetail', [])  )){
            //si on vient de ajouter a la collection 
            $detailBook = $this->session->get('bookDetail', []);
            $this->session->set('bookDetail', ''); 
        }  
        else{
            //si on vient de la page liste
            $detailBook = json_decode($request->request->get('detailLivre'), true);  
        }      


        //on recupere les collections du user coonnecte
        $user = $this->getUser();
        //ca marche pas ...
        // $collectionsUser = $user->getCollectionns();
        
        $collectionns = $collectionnRepository->findBy( array('collector' => $user ) );
        // dd($collectionns);

        return $this->render('BDboom/detail.html.twig', [
            'detailBook' => $detailBook,
            'collectionns' => $collectionns,
            'bdsearch' => $bdsearch,
        ]);
    }







    // AJOUTER UN LIVRE A SA COLLECTION ou SA WISHLIST :: mutualisation de la fonction
    #[Route('/addItemToCollectionOrWishlist', name: 'app_BDboom_addItemToCollectionOrWishlist', methods: ['GET', 'POST'])]
    public function addItemToCollection(UserRepository $userRepository, BDboomAPIsearchRepository $BDboomAPIsearchRepository, Request $request, AlbumRepository $albumRepository, BDboomRepository $BDboomRepository, AlbumCollectionRepository $albumCollectionRepository, CollectionnRepository $collectionnRepository, WishlistRepository $wishlistRepository): Response
    {       
        
        $addTo = $request->request->get('addTo');
        $bdsearch = $request->request->get('bdsearch');

        //session pour passage variable apres re routage
        $this->session->set('bdsearch', $bdsearch);

        $arrayBookInfo = json_decode($request->request->get('infoDetailArray'), true);
        
        // dd($arrayBookInfo);

        //date
        $Now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));       
        
        //on verifie si le livre n'est pas deja en BDD ::  (recherche par isbn) ET si isbn different de no isbn, 
        //eventuellement rechercher par ref amazon ou ref google book
        if( $arrayBookInfo['isbn'] != "no isbn"){
            $trouve = $albumRepository->findBy( array('isbn' => $arrayBookInfo['isbn'] ));
        }        
        if(empty($trouve)){
            $trouve = $albumRepository->findBy( array('title' => $arrayBookInfo['title'] ));
        }           
        
        //si le livre n'est pas en BDD, on enregistre le livre dans la table livre
        if(empty($trouve)){

            //on enregistre l'image sur le serveur ::  que si pas deja existant
            $newPathCover = $BDboomRepository->imageLoad($arrayBookInfo['cover']);

            $album = new Album;
            $album->setTitle($arrayBookInfo['title']);
            $album->setCover($newPathCover);
            $album->setDescription($arrayBookInfo['description']);
            $album->setRefBDfugues($arrayBookInfo['refBDfugue']);
            $album->setRefAmazon($arrayBookInfo['refAmazone']);
            $album->setIsbn($arrayBookInfo['isbn']);
            $album->setKeyword( $bdsearch." ".$arrayBookInfo['title'] );
            $album->setAuthor($arrayBookInfo['author']);
            $album->setBDboomDate($Now);
            $album->setOrigine( $addTo );

            $albumRepository->save($album, true);   
            
            //recuperer l'id du nouveau livre
            $albumID = $album->getId();         

        }
        else{
             //recuperer l'id du livre deja en BDD
            $albumID = $trouve[0]->getId();
        }      

        // dd($addTo);

        
        //on enregistre le livre dans la collection du user
        if($addTo == "collection"){
            //recuperer l'id de la collection        
            $collectionnIdSelected = $request->request->get('collectionn');        

            $albumObj = $albumRepository->findOneBy( array('id' => $albumID ));
            $collectionObj = $collectionnRepository->findOneBy( array('id' => $collectionnIdSelected ));

            $albumObj->addCollectionn($collectionObj);

            $albumRepository->save($albumObj, true);       

            //ajout d'un message flash
            $this->addFlash('albumAjout', 'Bravo, l album a été ajoutée à votre collection');
            
        }

        if($addTo == "wishlist"){
 
            // $wishlist = new Wishlist;
            $user = $this->getUser();        
            $wishlistObj = $wishlistRepository->findOneBy( ['collector' =>  $user ]);                 
    
            $albumObj = $albumRepository->findOneBy( array('id' => $albumID ));
               
            $albumObj->addWishlist($wishlistObj);
            $albumRepository->save($albumObj, true);
    
            //ajout d'un message flash
            $this->addFlash('albumAjout', 'Bravo, l album a été ajoutée à votre wishlist');
        }


        //retour sur la page precedente (pahe liste ou page detail)
        $previousUrl = $_SERVER['HTTP_REFERER'];

        $pos1 = strpos($previousUrl, 'detail');
        $pos2 = strpos($previousUrl, 'listeResultat');

        // dd($pos1, $pos2);

        //page detail
        if($pos1 != false){
            //session pour retour information sur page detail
            $this->session->set('bookDetail', $arrayBookInfo);    
        
            //on retourne sur la meme page detail        
            $splitUrl = explode("detail/", $previousUrl);
            $myUrlParam = explode("/", $splitUrl[1]);
        
            return $this->redirectToRoute('app_BDboom_detail', ['product'=>$myUrlParam[0] , 'id' => $myUrlParam[1] ], Response::HTTP_SEE_OTHER);
        }

        //page liste
        if($pos2 != false){            

            $listItemsBDboom = json_decode($request->request->get('listItemsBDboom'), true);
            $listItemsAmazon = json_decode($request->request->get('listItemsAmazon'), true);
            $listItemsGGbook = json_decode($request->request->get('listItemsGGbook'), true);

            //session pour retour information sur page liste
            $this->session->set('listItemsBDboom', $listItemsBDboom);
            $this->session->set('listItemsAmazon', $listItemsAmazon);
            $this->session->set('listItemsGGbook', $listItemsGGbook);
        
            //on retourne sur la meme page liste        
                
            return $this->redirectToRoute('app_BDboom_listeResultat',[], Response::HTTP_SEE_OTHER);
        }
      
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
