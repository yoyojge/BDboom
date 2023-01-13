<?php

namespace App\Repository;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;


// require_once( 'amazonAPI/vendor/autoload.php'); 


class BDboomAPIsearchRepository  extends AbstractController
{
    

    public function APIsearch($keyword)
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
            SearchItemsResource::OFFERSLISTINGSPRICE,
            SearchItemsResource::ITEM_INFOEXTERNAL_IDS,
            SearchItemsResource::ITEM_INFOFEATURES,
            SearchItemsResource::ITEM_INFOPRODUCT_INFO,
            SearchItemsResource::ITEM_INFOBY_LINE_INFO,

            // SearchItemsResource::ITEM_INFOCONTENT_INFO,
            // SearchItemsResource::ITEM_INFOCONTENT_RATING,
            // SearchItemsResource::ITEM_INFOCLASSIFICATIONS,
            // SearchItemsResource::ITEM_INFOMANUFACTURE_INFO,
            // SearchItemsResource::ITEM_INFOTECHNICAL_INFO,
            // SearchItemsResource::ITEM_INFOTRADE_IN_INFO,
    
        
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
            //         // if ($item->getASIN() !== null) {
            //         //     echo "ASIN: ", $item->getASIN(), PHP_EOL;
            //         // }
            //         // if ($item->getDetailPageURL() !== null) {
            //         //     echo "DetailPageURL: ", $item->getDetailPageURL();
            //         // }
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
                echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
                echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
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

        return $searchItemsResponse;
        // return $keyword;

    }




    public function scrappThis($url)
    {   
        return $url;
    }
}
