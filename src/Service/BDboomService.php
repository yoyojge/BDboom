<?php

namespace App\Service;

use App\Entity\User;

use App\Entity\Album;
// use Psr\Container\ContainerInterface;
use \Mailjet\Resources;
use App\Repository\AlbumRepository;
use App\Repository\BDboomRepository;
use App\Repository\WishlistRepository;

use App\Repository\CollectionnRepository;

use App\Repository\AlbumCollectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BDboomService extends AbstractController {

     private $albumCollectionRepository;
     private $albumRepository;

     public function __construct( AlbumRepository $albumRepository, AlbumCollectionRepository $albumCollectionRepository, BDboomRepository $BDboomRepository, CollectionnRepository $collectionnRepository, WishlistRepository $wishlistRepository) 
     {
          $this->albumCollectionRepository = $albumCollectionRepository;
          $this->albumRepository = $albumRepository;
          $this->BDboomRepository = $BDboomRepository;
          $this->collectionnRepository = $collectionnRepository;
          $this->wishlistRepository = $wishlistRepository;
         
          
     }
 


     public function searchInBDboom($arrayBookInfo) 
     {
          
          //on verifie si le livre n'est pas deja en BDD 
          //recherche par isbn et titre (isbn different de no isbn) 
          
          if( $arrayBookInfo['isbn'] != "no isbn"){
               $trouve = $this->albumRepository->findBy( array('isbn' => $arrayBookInfo['isbn'] ));
          }        
          if(empty($trouve)){
               $trouve = $this->albumRepository->findBy( array('title' => $arrayBookInfo['title'] ));
          }
          return $trouve;        
     }


     public function saveInBDboom($arrayBookInfo, $bdsearch, $addFrom) 
     {
          //on enregistre l'image sur le serveur 
          $newPathCover = $this->BDboomRepository->imageLoad($arrayBookInfo['cover']);

          $Now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

          // on instancie un nouvel objet album
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
          $album->setOrigine( $addFrom );

          //on enregistre cette nouvelle istance en BDD
          $this->albumRepository->save($album, true);   
          
          //recuperer l'id du nouveau livre
          $albumID = $album->getId();    
          
          return $albumID;
     }




     public function addAnIdBookToAnIdCollection($collectionnIdSelected, $albumID) 
     {
             
          $collectionObj = $this->collectionnRepository->findOneBy( array('id' => $collectionnIdSelected ));     

          $albumObj = $this->albumRepository->findOneBy( array('id' => $albumID ));            

          $albumObj->addCollectionn($collectionObj);
          $this->albumRepository->save($albumObj, true); 
     }




     public function suppAnIdBookToAnIdCollection($collectionnIdSelected, $albumID) 
     {
             
          $collectionObj = $this->collectionnRepository->findOneBy( array('id' => $collectionnIdSelected ));     

          $albumObj = $this->albumRepository->findOneBy( array('id' => $albumID ));            

          $albumObj->removeCollectionn($collectionObj);
          $this->albumRepository->save($albumObj, true); 
     }




     public function suppAnIdBookToAnIdWishlist($wishlistIdSelected, $albumID) 
     {
             
          $wishlistObj = $this->wishlistRepository->findOneBy( array('id' => $wishlistIdSelected ));     

          $albumObj = $this->albumRepository->findOneBy( array('id' => $albumID ));            

          $albumObj->removeWishlist($wishlistObj);
          $this->albumRepository->save($albumObj, true); 
     }




     public function mailJetSend01($token, $user) 
     {
             
          // require 'vendor/autoload.php';
          // dd($token, $user,$user->getEmail() );

          // Use your saved credentials, specify that you are using Send API v3.1
           # Please add your access key here cle renseignées dans .env et config/service.yaml         
          $MJ_APIKEY_PUBLIC =  $this->getParameter('app.mailJetkey');
          
          # Please add your secret key here
          $MJ_APIKEY_PRIVATE =  $this->getParameter('app.mailJetsecretkey');


          $mj = new \Mailjet\Client($MJ_APIKEY_PUBLIC, $MJ_APIKEY_PRIVATE,true,['version' => 'v3.1']);
          // dd($mj);
          // Define your request body

         

          $body = [
               'Messages' => [
                    [
                         'From' => [
                              'Email' => "info@bdboom.fr",
                              'Name' => "BDboom"
                         ],
                         'To' => [
                              [
                                   'Email' => $user->getEmail(),
                                   'Name' => $user->getFirstName()." ".$user->getLastName()
                              ]
                         ],
                         'TemplateID'=> 4595050,
                         'TemplateLanguage' => true,
                         'Subject' => 'Bienvenu sur BDboom',
                         // 'Variables' => json_decode('{
                         //           "urlConf": "http://bdboom.test/confirmationInscription?token="'.$user->getToken().'
                         //      }', true)
                         'Variables' => [
                              'urlConf' => $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].'/confirmationInscription?token='.$user->getToken().''
                         ]
                    ]
                         
               ]
          
          ];

          // 'TextPart' => "Bienvenu sur BDboom",
          // 'HTMLPart' => "<h3>Bonjour, Bienvenu sur BDboom,</h3><br />
          // Pour complètement valider votre compte merci de cliquer sur le lien suivant:<br />
          // <a href=\"http://bdboom.test/confirmationInscription?token=".$token."\">Confirmez votre compte</a>!
          // <br />
          // ",
          // <a href=\"' . $this->generateUrl('app_BDboom_confirmationInscription', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL) . '\">Activer mon compte</a>
          // All resources are located in the Resources class

          $response = $mj->post(Resources::$Email, ['body' => $body]);
          // dd($response);
          // Read the response

          $response->success() && var_dump($response->getData());
     }







     public function mailJetSendTest() 
     {           
          $MJ_APIKEY_PUBLIC =  $this->getParameter('app.mailJetkey');
          $MJ_APIKEY_PRIVATE =  $this->getParameter('app.mailJetsecretkey');

          $mj = new \Mailjet\Client($MJ_APIKEY_PUBLIC, $MJ_APIKEY_PRIVATE,true,['version' => 'v3.1']);

          $body = [
          'Messages' => [
               [
                    'From' => [
                         'Email' => "info@bdboom.fr",
                         'Name' => "BDboom"
                    ],
                    'To' => [
                         [
                         'Email' => "johann.griffe.pro@gmail.com",
                         'Name' => "yoyo"
                         ]
                    ],
                    'Subject' => "Bienvenu sur BDboom",
                    'TextPart' => "Bienvenu sur BDboom",
                    'HTMLPart' => "<h3>Bonjour, Bienvenu sur BDboom,</h3><br />
                    <br />
                    ",  
               ]
          ]
          ];

          $response = $mj->post(Resources::$Email, ['body' => $body]);
          // $response->success() && var_dump($response->getData());
          // dd($response);
     }






}