<?php

namespace App\Repository;


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

// use App\Entity\Album;
// use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// use Doctrine\Persistence\ManagerRegistry;

/**
 * 
 *
 */

class BDboomRepository 
{
    

    public function RSS($rssFeed )
    {             
       
        $dom = new \DOMDocument();
        if (!$dom->load($rssFeed)) {
            die('Impossible de charger le fichier XML');
        }

        $itemList = $dom->getElementsByTagName('item');

        $tabRss = [];

        $titre = $itemList[0]->getElementsByTagName('title');
        if ($titre->length > 0) {
            
            $tabRss['titre'] = $titre->item(0)->nodeValue;
        } else {

            $tabRss['titre'] = "Titre";
        }
       

        $lien = $itemList[0]->getElementsByTagName('link');
        if ($lien->length >0) {
            $tabRss['lien'] = $lien->item(0)->nodeValue;
        } 

        $desc = $itemList[0]->getElementsByTagName('description');

        if ($desc->length > 0) {     

            foreach($desc as $node) {
                $nodes[] = $node;
            }
            
            $stringAsXml = "<?xml version='1.0'?><layout>".$nodes[0]->nodeValue."</layout>";
            
            $array = simplexml_load_string(html_entity_decode($stringAsXml), 'SimpleXMLElement',  LIBXML_NOCDATA );

            $tabRss['img'] = $array[0]->{'p'}[0]->img['src'];
            $tabRss['desc'] = $array[0]->{'p'}[1];
           
        }

        return $tabRss;
        

    }




    function imageLoad($imagePath){

        // preg_match_all('/\bhttps?:\/\/\S+(?:png|jpg)\b/', $imagePath, $matches);
        $unique = uniqid();


        // dd($imagePath, $matches);

        // if(!empty($matches[0])){
        //     $img = '../public/images/book/'. $unique.''.$matches[0];
        // } 
        // else{
        //     $img = '../public/images/book/'.$unique.'.png';
        // }       
        
        $img = '../public/images/book/'.$unique.'.png';
        $imgName = ''.$unique.'.png';
        // Enregistrer l'image
        file_put_contents($img, file_get_contents($imagePath));
        return $imgName;

    }


    function objectToArray($arrayOfObjectsBDboom){
        $listItemsBDboom = [];
        for($i=0;$i < count($arrayOfObjectsBDboom); $i++){


            $detailBook[$i]['title'] = $arrayOfObjectsBDboom[$i]->getTitle();
            if($arrayOfObjectsBDboom[$i]->getCover() != "no cover"){
                $detailBook[$i]['cover'] = "/images/book/".$arrayOfObjectsBDboom[$i]->getCover();
            } 
            else{
                $detailBook[$i]['cover'] = $arrayOfObjectsBDboom[$i]->getCover();
            }     
            $detailBook[$i]['description'] = $arrayOfObjectsBDboom[$i]->getDescription();  
            $detailBook[$i]['author'] = $arrayOfObjectsBDboom[$i]->getAuthor();
            $detailBook[$i]['isbn'] = $arrayOfObjectsBDboom[$i]->getIsbn(); 
            $detailBook[$i]['detailPageUrl'] =""; 
            $detailBook[$i]['price'] =""; 
            $detailBook[$i]['refBDfugue'] = $arrayOfObjectsBDboom[$i]->getRefBDfugues();
            $detailBook[$i]['refAmazone'] = $arrayOfObjectsBDboom[$i]->getRefAmazon();

        }
        return $detailBook;

    }


    

    

   
}
