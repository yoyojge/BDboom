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

        preg_match_all('/\bhttps?:\/\/\S+(?:png|jpg)\b/', $imagePath, $matches);
        $unique = uniqid();

        if(!empty($matches[0])){
            $img = '../public/images/book/'. $unique.''.$matches[0];
        } 
        else{
            $img = '../public/images/book/'.$unique.'.png';
        }       
        
        // Enregistrer l'image
        file_put_contents($img, file_get_contents($imagePath));
        return $img;

    }

   
}
