<?php

namespace App\Repository;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\Configuration;


// require_once( 'amazonAPI/vendor/autoload.php'); 


class BDboomAPIsearchRepository 
{
    

    public function APIsearch($keyword)
    {             
        $config = new Configuration();

        # Please add your access key here
        $config->setAccessKey('AKIAI365V5GY3E42TM2A');
        # Please add your secret key here
        $config->setSecretKey('hBBZMWp1pJaO0BFTjNjT/CBxprM5EAMdNwYkPoOK');
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
        $resources = [
            SearchItemsResource::ITEM_INFOTITLE,
            SearchItemsResource::OFFERSLISTINGSPRICE];

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

            echo 'API called successfully', PHP_EOL;
            echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;

            # Parsing the response
            if ($searchItemsResponse->getSearchResult() !== null) {
                echo 'Printing first item information in SearchResult:', PHP_EOL;
                $item = $searchItemsResponse->getSearchResult()->getItems()[0];
                if ($item !== null) {
                    if ($item->getASIN() !== null) {
                        echo "ASIN: ", $item->getASIN(), PHP_EOL;
                    }
                    if ($item->getDetailPageURL() !== null) {
                        echo "DetailPageURL: ", $item->getDetailPageURL(), PHP_EOL;
                    }
                    if ($item->getItemInfo() !== null
                        and $item->getItemInfo()->getTitle() !== null
                        and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
                        echo "Title: ", $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                    }
                    if ($item->getOffers() !== null
                        and $item->getOffers() !== null
                        and $item->getOffers()->getListings() !== null
                        and $item->getOffers()->getListings()[0]->getPrice() !== null
                        and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
                        echo "Buying price: ", $item->getOffers()->getListings()[0]->getPrice()
                            ->getDisplayAmount(), PHP_EOL;
                    }
                }
            }
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

        return $searchItemsRequest;
        // return $keyword;

    }

   
}
