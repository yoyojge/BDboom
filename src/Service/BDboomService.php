<?php

namespace App\Service;

use App\Entity\Album;

use App\Repository\AlbumRepository;
// use Psr\Container\ContainerInterface;
use App\Repository\BDboomRepository;
use App\Repository\CollectionnRepository;
use App\Repository\AlbumCollectionRepository;


class BDboomService {

     private $albumCollectionRepository;
     private $albumRepository;

     public function __construct( AlbumRepository $albumRepository, AlbumCollectionRepository $albumCollectionRepository, BDboomRepository $BDboomRepository, CollectionnRepository $collectionnRepository) 
     {
          $this->albumCollectionRepository = $albumCollectionRepository;
          $this->albumRepository = $albumRepository;
          $this->BDboomRepository = $BDboomRepository;
          $this->collectionnRepository = $collectionnRepository;
          
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


     public function saveInBDboom($arrayBookInfo, $bdsearch, $addTo) 
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
          $album->setOrigine( $addTo );

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


}