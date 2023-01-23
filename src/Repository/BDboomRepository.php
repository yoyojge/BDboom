<?php

namespace App\Repository;


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
        // Enregistrer l'image
        file_put_contents($img, file_get_contents($imagePath));
        return $img;

    }





    function bookInfo($sessSearchDetail, $sessSearchFrom){

        //si from BDboom
        if($sessSearchFrom == "BDboom"){
            
        }



        //si from Amazon
        if($sessSearchFrom == "amazon"){
           
            $title = $sessSearchDetail['itemInfo']['title']['displayValue'];
            $arrayBookInfo['title']  = $title;
           
            //detail
            if(!empty($sessSearchDetail['itemInfo']['features'])){
                $description = $sessSearchDetail['itemInfo']['features'];                
            }
            else{
                $description = "no description";
            }
            $arrayBookInfo['description']  = $description;

            //isbn
            if(!empty($sessSearchDetail['itemInfo']['externalIds']['iSBNs'])){
                $isbn = $sessSearchDetail['itemInfo']['externalIds']['iSBNs']['displayValues'][0];
            }
            else{
                $isbn = "no isbn";
            }
            $arrayBookInfo['isbn']  = $isbn;

            //image
            if(!empty($sessSearchDetail['images']['primary']['large'])){
                $coverOnline = $sessSearchDetail['images']['primary']['large']['uRL'];
            }
            else{
                $coverOnline = "no cover";
            }
            $arrayBookInfo['coverOnline']  = $coverOnline;

            //author
            if(!empty($sessSearchDetail['itemInfo']['byLineInfo']['contributors'])){
                $authors = $sessSearchDetail['itemInfo']['byLineInfo']['contributors'][0];                
            }
            else{
                $authors = "";
            }  
            $arrayBookInfo['authors']  = $authors;

            $refBDfugue = ""; 
            $arrayBookInfo['refBDfugue']  = $refBDfugue;

            $refAmazone = $sessSearchDetail['aSIN']; 
            $arrayBookInfo['refAmazone']  = $refAmazone;        
        }


        //si from GGbook
        if($sessSearchFrom == "ggbook"){
            
            $title = $sessSearchDetail['volumeInfo']['title']; 
            $arrayBookInfo['title']  = $title;           
            
            //detail
            if(!empty($sessSearchDetail['volumeInfo']['description'])){
                $description = $sessSearchDetail['volumeInfo']['description'];
            }
            else{
                $description = "no description";
            }
            $arrayBookInfo['description']  = $description;

            //isbn
            if(!empty($sessSearchDetail['volumeInfo']['industryIdentifiers'])){
                $isbn = $sessSearchDetail['volumeInfo']['industryIdentifiers'][0]['identifier'];
            }
            else{
                $isbn = "no isbn";
            }
            $arrayBookInfo['isbn']  = $isbn;

            //image
            if(!empty($sessSearchDetail['volumeInfo']['imageLinks'])){
                $coverOnline = $sessSearchDetail['volumeInfo']['imageLinks']['thumbnail'];
            }
            else{
                $coverOnline = "no cover";
            }
            $arrayBookInfo['coverOnline']  = $coverOnline;

            //author
            if(!empty($sessSearchDetail['volumeInfo']['authors'])){
                $authors = $sessSearchDetail['volumeInfo']['authors'][0];                
            }
            else{
                $authors = "";
            }  
            $arrayBookInfo['authors']  = $authors;

            $refBDfugue = ""; 
            $arrayBookInfo['refBDfugue']  = $refBDfugue;

            $refAmazone = ""; 
            $arrayBookInfo['refAmazone']  = $refAmazone;                        
            
        }


        return $arrayBookInfo;

    }

   
}
