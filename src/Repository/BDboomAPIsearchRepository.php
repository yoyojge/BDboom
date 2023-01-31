<?php

namespace App\Repository;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;


// require_once( 'amazonAPI/vendor/autoload.php'); 


class BDboomAPIsearchRepository  extends AbstractController
{
    

    public function APIsearchAmazon($keyword)
    {             
        $config = new Configuration();

        # Please add your access key here cle renseignées dans .env et service.yaml
        $config->setAccessKey( $this->getParameter('app.amazonaccesskey') );
        # Please add your secret key here
        $config->setSecretKey( $this->getParameter('app.amazonsecretkey') );
        # Please add your partner tag (store/tracking id) here
        $partnerTag = 'bdboom04-21';

        $config->setHost('webservices.amazon.fr');
        $config->setRegion('eu-west-1');

        $apiInstance = new DefaultApi(
            /*
            * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            * This is optional, `GuzzleHttp\Client` will be used as default.
            */
            new \GuzzleHttp\Client(),
            $config
        );

 

        $searchIndex = "Books";

        # Specify item count to be returned in search result
        $itemCount = 10;

        /*
        * Choose resources you want from SearchItemsResource enum
        * For more details,
        * refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
        */

        /*
            liste des constantes dans le fichier
            C:\000-yoyo\FORMATION-DWWM-9B\BDboom\amazonCredentials\paapi5-php-sdk-example\paapi5-php-sdk-example\src\com\amazon\paapi5\v1\SearchItemsResource.php
            IMAGESPRIMARYMEDIUM pour les images 
            ITEM_INFOEXTERNAL_IDS pour l'isbn
            ITEM_INFOPRODUCT_INFO pour savoir si c'est du contenu pour adulte
            ITEM_INFOBY_LINE_INFO pour connaitre l'auteur

        */

        $resources = [
            SearchItemsResource::ITEM_INFOTITLE,
            SearchItemsResource::IMAGESPRIMARYMEDIUM,
            SearchItemsResource::IMAGESPRIMARYLARGE,
            SearchItemsResource::OFFERSLISTINGSPRICE,
            SearchItemsResource::ITEM_INFOEXTERNAL_IDS,
            SearchItemsResource::ITEM_INFOFEATURES,
            SearchItemsResource::ITEM_INFOPRODUCT_INFO,
            SearchItemsResource::ITEM_INFOBY_LINE_INFO,

            SearchItemsResource::ITEM_INFOCONTENT_INFO,
            SearchItemsResource::ITEM_INFOCONTENT_RATING,
            SearchItemsResource::ITEM_INFOCLASSIFICATIONS,
            SearchItemsResource::ITEM_INFOMANUFACTURE_INFO,
            SearchItemsResource::ITEM_INFOTECHNICAL_INFO,
            SearchItemsResource::ITEM_INFOTRADE_IN_INFO,

            SearchItemsResource::ITEM_INFOTRADE_IN_INFO,
    
        
        ];

        # Forming the request
        $searchItemsRequest = new SearchItemsRequest();
        $searchItemsRequest->setSearchIndex($searchIndex);
        $searchItemsRequest->setKeywords($keyword);
        $searchItemsRequest->setItemCount($itemCount);
        $searchItemsRequest->setPartnerTag($partnerTag);
        $searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $searchItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $searchItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            echo "Error forming the request", PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
            }
            return;
        }

        # Sending the request
        try {
            $searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);

            // echo 'API called successfully', PHP_EOL;
            // echo 'Complete Response: <br /><br /> ', $searchItemsResponse, PHP_EOL;
            // echo "<br /><br />";


            # Parsing the response
            // if ($searchItemsResponse->getSearchResult() !== null) {
            //     echo 'Printing first item information in SearchResult:';
            //     $item = $searchItemsResponse->getSearchResult()->getItems()[0];
            //     if ($item !== null) {
            //         if ($item->getASIN() !== null) {
            //             echo "ASIN: ", $item->getASIN(), PHP_EOL;
            //         }
            //         if ($item->getDetailPageURL() !== null) {
            //             echo "DetailPageURL: ", $item->getDetailPageURL();
            //         }
            //         if ($item->getItemInfo() !== null
            //             and $item->getItemInfo()->getTitle() !== null
            //             and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
            //             echo "Title: ", $item->getItemInfo()->getTitle()->getDisplayValue();
            //         }
            //         if ($item->getOffers() !== null
            //             and $item->getOffers() !== null
            //             and $item->getOffers()->getListings() !== null
            //             and $item->getOffers()->getListings()[0]->getPrice() !== null
            //             and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
            //             echo "Buying price: ", $item->getOffers()->getListings()[0]->getPrice()
            //                 ->getDisplayAmount();
            //         }

            //         echo "<br /><br /><br /><br />";
            //     }
            // }
            if ($searchItemsResponse->getErrors() !== null) {
                // echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                // echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
                // echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
            }
        } catch (ApiException $exception) {
            // echo "Error calling PA-API 5.0!", PHP_EOL;
            // echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
            // echo "Error Message: ", $exception->getMessage(), PHP_EOL;
            // if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
            //     $errors = $exception->getResponseObject()->getErrors();
            //     foreach ($errors as $error) {
            //         echo "Error Type: ", $error->getCode(), PHP_EOL;
            //         echo "Error Message: ", $error->getMessage(), PHP_EOL;
            //     }
            // } else {
            //     echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
            // }
        } catch (Exception $exception) {
            // echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        }


        if(!empty($searchItemsResponse)){
            $listItems = $searchItemsResponse['searchResult']['items'];
        }
        else{
            $listItems = [];
        }

        // dd( $listItems);

        return $listItems;
        // return $keyword;

    }









    //fonction utilisée dans la fonction DetailAmazonProduct
    public function parseResponse($items)
    {
        $mappedResponse = [];
        foreach ($items as $item) {
            $mappedResponse[$item->getASIN()] = $item;
        }
        return $mappedResponse;
    }


    public function DetailAmazonProduct($itemIds)
    { 
        
        
        $config = new Configuration();

        $config->setAccessKey( $this->getParameter('app.amazonaccesskey') );
        $config->setSecretKey( $this->getParameter('app.amazonsecretkey') );
        $partnerTag = 'bdboom04-21';

        $config->setHost('webservices.amazon.com');
        $config->setRegion('eu-west-1');

        $apiInstance = new DefaultApi(
            new \GuzzleHttp\Client(),
            $config
        );

    # Request initialization

    # Choose item id(s)
    // $itemIds = ["2017168769"];

    $resources = [
        GetItemsResource::ITEM_INFOTITLE,
        GetItemsResource::OFFERSLISTINGSPRICE,
        GetItemsResource::ITEM_INFOFEATURES,
    ];

    # Forming the request
    $getItemsRequest = new GetItemsRequest();
    $getItemsRequest->setItemIds($itemIds);
    $getItemsRequest->setPartnerTag($partnerTag);
    $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
    $getItemsRequest->setResources($resources);

    # Validating request
    $invalidPropertyList = $getItemsRequest->listInvalidProperties();
    $length = count($invalidPropertyList);
    if ($length > 0) {
        echo "Error forming the request", PHP_EOL;
        foreach ($invalidPropertyList as $invalidProperty) {
            echo $invalidProperty, PHP_EOL;
        }
        return;
    }

    # Sending the request
    try {
        $getItemsResponse = $apiInstance->getItems($getItemsRequest);

        // echo 'API called successfully', PHP_EOL;
        // echo 'Complete Response: ', $getItemsResponse, PHP_EOL;

        # Parsing the response
        if ($getItemsResponse->getItemsResult() !== null) {
            echo 'Printing all item information in ItemsResult:', PHP_EOL;
            if ($getItemsResponse->getItemsResult()->getItems() !== null) {
                $responseList = parseResponse($getItemsResponse->getItemsResult()->getItems());

                foreach ($itemIds as $itemId) {
                    echo 'Printing information about the itemId: ', $itemId, PHP_EOL;
                    $item = $responseList[$itemId];
                    if ($item !== null) {
                        if ($item->getASIN()) {
                            echo 'ASIN: ', $item->getASIN(), PHP_EOL;
                        }
                        if ($item->getItemInfo() !== null and $item->getItemInfo()->getTitle() !== null
                            and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
                            echo 'Title: ', $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                        }
                        if ($item->getDetailPageURL() !== null) {
                            echo 'Detail Page URL: ', $item->getDetailPageURL(), PHP_EOL;
                        }
                        if ($item->getOffers() !== null and
                            $item->getOffers()->getListings() !== null
                            and $item->getOffers()->getListings()[0]->getPrice() !== null
                            and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
                            echo 'Buying price: ', $item->getOffers()->getListings()[0]->getPrice()
                                ->getDisplayAmount(), PHP_EOL;
                        }
                    } else {
                        echo "Item not found, check errors", PHP_EOL;
                    }
                }
            }
        }
        if ($getItemsResponse->getErrors() !== null) {
            echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
            echo 'Error code: ', $getItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
            echo 'Error message: ', $getItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
        }
    } catch (ApiException $exception) {
        echo "Error calling PA-API 5.0!", PHP_EOL;
        echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
        echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
            $errors = $exception->getResponseObject()->getErrors();
            foreach ($errors as $error) {
                echo "Error Type: ", $error->getCode(), PHP_EOL;
                echo "Error Message: ", $error->getMessage(), PHP_EOL;
            }
        } else {
            echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
        }
    } catch (Exception $exception) {
        echo "Error Message: ", $exception->getMessage(), PHP_EOL;
    }

        dd('coucou');

        return $url;
    }





    public function APIcleanAmazonData($listItemsAmazonBrut)
    { 

       
        for($i=0; $i<count($listItemsAmazonBrut); $i++){
            $detailBook[$i]['title'] = $listItemsAmazonBrut[$i]['itemInfo']['title']['displayValue'];
            $detailBook[$i]['cover'] = $listItemsAmazonBrut[$i]['images']['primary']['large']['uRL'];
            $detailBook[$i]['description'] = "";
            //boucle sur le tableau pour recuperer toutes les infos
            $detailBook[$i]['author'] = "";
            for($j = 0; $j < count($listItemsAmazonBrut[$i]['itemInfo']['byLineInfo']['contributors']); $j++){
                $detailBook[$i]['author'] .= $listItemsAmazonBrut[$i]['itemInfo']['byLineInfo']['contributors'][$j]['name'].", ";
            }  
            if(!empty($listItemsAmazonBrut[$i]['itemInfo']['externalIds']['iSBNs'])){
                $detailBook[$i]['isbn'] = $listItemsAmazonBrut[$i]['itemInfo']['externalIds']['iSBNs']['displayValues'][0];   
            }         
            else{
                $detailBook[$i]['isbn'] = "no isbn";
            }                   
            $detailBook[$i]['detailPageUrl'] = $listItemsAmazonBrut[$i]['detailPageURL'];
            $detailBook[$i]['price'] = $listItemsAmazonBrut[$i]['offers']['listings'][0]['price']['displayAmount']; 
            $detailBook[$i]['refBDfugue'] = "";
            $detailBook[$i]['refAmazone'] = "";

        }
        // dd($detailBook);
        return $detailBook;
    }








    public function APIsearchGoogle($bdsearch)
    {        
        $bdsearchGGbook = str_replace(" ","+",$bdsearch);
        $urlGGbook = 'https://www.googleapis.com/books/v1/volumes?q='.$bdsearchGGbook.'&key='.$this->getParameter('app.googleapikey');
        $listDecodeGGbook = json_decode(file_get_contents($urlGGbook), true); 
        $listItemsGGbook = $listDecodeGGbook['items'];   
        return   $listItemsGGbook;
    }


    public function APIcleanGoogle($listItemsGGbookBrut)
    { 
       
        for($i=0; $i<count($listItemsGGbookBrut); $i++){
            $detailBook[$i]['title'] = $listItemsGGbookBrut[$i]['volumeInfo']['title'];
            if(!empty($listItemsGGbookBrut[$i]['volumeInfo']['imageLinks']['thumbnail'])){
                $detailBook[$i]['cover'] = $listItemsGGbookBrut[$i]['volumeInfo']['imageLinks']['thumbnail'];
            }
            else{
                $detailBook[$i]['cover'] = "no cover";
            }

            if(!empty($listItemsGGbookBrut[$i]['volumeInfo']['description'])){
                $detailBook[$i]['description'] = $listItemsGGbookBrut[$i]['volumeInfo']['description'];
            }
            else{
                $detailBook[$i]['description'] = "";
            }
            //boucle sur le tableau pour recuperer toutes les infos
            $detailBook[$i]['author'] = "";
            if(!empty($listItemsGGbookBrut[$i]['volumeInfo']['authors'])){
                for($j = 0; $j < count($listItemsGGbookBrut[$i]['volumeInfo']['authors']); $j++){
                    $detailBook[$i]['author'] .= $listItemsGGbookBrut[$i]['volumeInfo']['authors'][$j].", ";
                } 
            }
            if(!empty($listItemsGGbookBrut[$i]['volumeInfo']['industryIdentifiers'][1])){       
                $detailBook[$i]['isbn'] = $listItemsGGbookBrut[$i]['volumeInfo']['industryIdentifiers'][1]['identifier'];  
            } 
            else{
                $detailBook[$i]['isbn'] = "no isbn";
            }                   
            $detailBook[$i]['detailPageUrl'] = "";
            $detailBook[$i]['price'] = "";   
            $detailBook[$i]['refBDfugue'] = "";
            $detailBook[$i]['refAmazone'] = "";

        }
        // dd($detailBook);
        return $detailBook;
    }





    // public function scrappThis($url)
    // {   
    //     return $url;
    // }
}
