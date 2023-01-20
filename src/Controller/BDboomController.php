<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Album;
use App\Form\UserType;
use App\Entity\AlbumCollection;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use App\Repository\BDboomRepository;
use App\Repository\CollectionnRepository;
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
        $sessSearchDetailIndex = $this->session->get('sessSearchDetailIndex', []);   
        
        //si from BDboom
        if($this->session->get('sessSearchFrom') == "BDboom"){
            // $title = $sessSearchDetail['volumeInfo']['title'];
            // $description = $sessSearchDetail['volumeInfo']['description'];
        }

        //si from Amazon
        if($this->session->get('sessSearchFrom') == "amazon"){
            // $title = $sessSearchDetail['volumeInfo']['title'];
            // $description = $sessSearchDetail['volumeInfo']['description'];
        }


        //si from GGbook
        if($this->session->get('sessSearchFrom') == "ggbook"){
            $title = $sessSearchDetail['volumeInfo']['title'];            
            
            //detail
            if(!empty($sessSearchDetail['volumeInfo']['description'])){
                $description = $sessSearchDetail['volumeInfo']['description'];
            }
            else{
                $description = "no description";
            }

            //isbn
            if(!empty($sessSearchDetail['volumeInfo']['industryIdentifiers'])){
                $isbn = $sessSearchDetail['volumeInfo']['industryIdentifiers'][0]['identifier'];
            }
            else{
                $isbn = "no isbn";
            }

            //image
            if(!empty($sessSearchDetail['volumeInfo']['imageLinks'])){
                $coverOnline = $sessSearchDetail['volumeInfo']['imageLinks']['thumbnail'];
                //on enregistre l'image sur le serveur
                $newPathCover = $BDboomRepository->imageLoad($coverOnline);
            }
            else{
                $cover = "no cover";
            }

            //author
            if(!empty($sessSearchDetail['volumeInfo']['authors'])){
                $authors = $sessSearchDetail['volumeInfo']['authors'][0];                
            }
            else{
                $authors = "";
            }  

            $refBDfugue = ""; 
            $refAmazone = "";                        
            
        }

        //date
        $Now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
       
        
        //on verifie si le livre n'est pas deja en BDD ::  (recherche par isbn)
        $trouve = $albumRepository->findBy( array('isbn' => $isbn ));
        // dd($isbn, $trouve);       
        
        //si le livre n'est pas en BDD, on enregistre le livre dans la table livre
        if(empty($trouve)){
            $album = new Album;
            $album->setTitle($title);
            $album->setCover($newPathCover.'.png');
            $album->setDescription($description);
            $album->setRefBDfugues($refBDfugue);
            $album->setRefAmazon($refAmazone);
            $album->setIsbn($isbn);
            $album->setKeyword( $this->session->get('sessKeywordSearch')." ".$title );
            $album->setAuthor($authors);
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
                
        

        //TODO:on enregistre le livre dans la collection du user
        //recuperer l'id de la collection
        $albumCollection = new AlbumCollection;
        
        $collectionnIdSelected = $request->request->get('collectionn');
        // dd($request->request->all());

        $albumObj = $albumRepository->findBy( array('id' => $albumID ));
        // dd($albumObj);
        $albumCollection->setAlbum( $albumObj[0]);

        $collectionObj = $collectionnRepository->findBy( array('id' => $collectionnIdSelected ));
        $albumCollection->setCollection($collectionObj[0]);
        
        $albumCollectionRepository->save($albumCollection, true);
        
        


        //ajout d'un message flash
        $this->addFlash('albumAjout', 'Bravo, l album a été ajoutée à votre collection');

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










    // TODO: PAGE 404 :: test chat en scss pour la 404
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
