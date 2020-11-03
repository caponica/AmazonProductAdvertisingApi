<?php

namespace CaponicaAmazonPaa\Client;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResponse;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResponse;
use CaponicaAmazonPaa\ParameterSet\GetItemsParameterSet;
use CaponicaAmazonPaa\ParameterSet\SearchItemsParameterSet;
use CaponicaAmazonPaa\Response\Item;

/**
 * Client to connect to the Amazon Product Advertising API
 */
class ApaaClient
{
    /**
     * @var ApaaClientConfiguration
     */
    private $configuration;

    private $apiInstance;

    public static function buildFromArray($configArray, $countryCode=null) {
        $configuration = ApaaClientConfiguration::buildFromArrayAndCountry($configArray, $countryCode);
        return new ApaaClient($configuration);
    }

    public function __construct(ApaaClientConfiguration $configuration) {
        $this->configuration = $configuration;
        $this->apiInstance = $configuration->createApiInstance();
    }

    public function getDomainSuffix() {
        return $this->configuration->getDomainSuffix();
    }
    public function getCountryCode() {
        return $this->configuration->getCountryCode();
    }

    /**
     * @param GetItemsRequest|SearchItemsRequest $requestObject
     */
    protected function validateRequestObject($requestObject) {
        $invalidPropertyList = $requestObject->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            $requestClass = get_class($requestObject);
            $errorMessage = "Error forming PAA5 $requestClass request" . PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                $errorMessage .= $invalidProperty . PHP_EOL;
            }
            throw new \InvalidArgumentException($errorMessage);
        }
    }

    /**
     * @param GetItemsRequest $getItemsRequest
     * @return GetItemsResponse
     * @throws \Exception
     */
    protected function validateAndCallGetItems(GetItemsRequest $getItemsRequest) {
        // Validate
        $this->validateRequestObject($getItemsRequest);
//print_r($getItemsRequest);
        # Sending the request
        try {
            $getItemsResponse = $this->apiInstance->getItems($getItemsRequest);
//echo 'PAA called successfully', PHP_EOL;
//echo 'Complete Response: ', $getItemsResponse, PHP_EOL;
        } catch (ApiException $exception) {
            $errorMessage = "\n*** ApiException ERROR calling PA-API 5.0 getItems ***";
            $errorMessage .= "\nHTTP Status Code: {$exception->getCode()}";
            $errorMessage .= "\nError Message: {$exception->getMessage()}";
            $errorMessage .= "\nRequest:\n" . print_r($getItemsRequest, true) . "\n";

            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {
                    $errorMessage .= "\n\nError Type: {$error->getCode()}";
                    $errorMessage .= "\nError Message: {$error->getMessage()}";
                }
            } else {
                $errorMessage .= "\n\nError response body: {$exception->getResponseBody()}";
            }
            throw new \Exception($errorMessage);
        }

        # Parsing the response
        if ($getItemsResponse->getErrors() !== null) {
            $errorMessage = "\nErrors returned from PAA5 API getItems:\n";
            foreach ($getItemsResponse->getErrors() as $errorObject) {
                $errorMessage .= "\nError #{$errorObject->getCode()}: {$errorObject->getMessage()}";
            }
            throw new \Exception($errorMessage);
        }

        return $getItemsResponse;
    }

    /**
     * @param string|array $itemIds
     * @param array $resources
     * @param array $extraParams
     * @param bool $returnItemsInsteadOfResponse
     * @return Item[]|GetItemsResponse
     * @throws \Exception
     */
    protected function getItems($itemIds, $resources=null, $extraParams=[], $returnItemsInsteadOfResponse=false) {
        if (!empty($itemIds) && !is_array($itemIds)) {
            $itemIds = [$itemIds];
        }

        $ps = GetItemsParameterSet::generateParameterSetNewOnly();
        $ps->mergeParameters($extraParams);

        $getItemsRequest = new GetItemsRequest($ps->getParameters());
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setResources($resources);
        $getItemsRequest->setPartnerTag($this->configuration->getPartnerTag());
        $getItemsRequest->setMarketplace($this->configuration->getMarketplace());

        try {
            $getItemsResponse = $this->validateAndCallGetItems($getItemsRequest);
        } catch (\Exception $e) {
            echo "\n# APAA EXCEPTION, first pass#\n";
            echo $e->getMessage();
            echo "\nWaiting 1 minute before re-trying\n";
            sleep(60);
            $getItemsResponse = $this->validateAndCallGetItems($getItemsRequest);
        }

        if ($returnItemsInsteadOfResponse) {
            $mappedResponse = [];
            if ($getItemsResponse->getItemsResult() !== null) {
                if ($getItemsResponse->getItemsResult()->getItems() !== null) {
                    foreach ($getItemsResponse->getItemsResult()->getItems() as $item) {
                        $mappedResponse[$item->getASIN()] = new Item($item);
                    }
                }
            }
            return $mappedResponse;
        }

        return $getItemsResponse;
    }

    protected function hasNoSearchTerms(SearchItemsRequest $searchItemsRequest) {
        if ($searchItemsRequest->getTitle()) {
            return false;
        }
        if ($searchItemsRequest->getKeywords()) {
            return false;
        }
        if ($searchItemsRequest->getActor()) {
            return false;
        }
        if ($searchItemsRequest->getArtist()) {
            return false;
        }
        if ($searchItemsRequest->getAuthor()) {
            return false;
        }
        if ($searchItemsRequest->getBrand()) {
            return false;
        }
        return true;
    }

    /**
     * @param SearchItemsRequest $searchItemsRequest
     * @return SearchItemsResponse
     * @throws \Exception
     */
    protected function validateAndCallSearchItems(SearchItemsRequest $searchItemsRequest) {
        if ($this->hasNoSearchTerms($searchItemsRequest)) {
            throw new \Exception('Must provide at least one search term in: Title, Keywords, Actor, Artist, Author or Brand');
        }
        // Validate
        $this->validateRequestObject($searchItemsRequest);
//print_r($searchItemsRequest);
        # Sending the request
        try {
            $searchItemsResponse = $this->apiInstance->searchItems($searchItemsRequest);
//echo 'PAA called successfully', PHP_EOL;
//echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;
        } catch (ApiException $exception) {
            $errorMessage = "\n*** ApiException ERROR calling PA-API 5.0 searchItems ***";
            $errorMessage .= "\nHTTP Status Code: {$exception->getCode()}";
            $errorMessage .= "\nError Message: {$exception->getMessage()}";

            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {
                    $errorMessage .= "\n\nError Type: {$error->getCode()}";
                    $errorMessage .= "\nError Message: {$error->getMessage()}";
                }
            } else {
                $errorMessage .= "\n\nError response body: {$exception->getResponseBody()}";
            }
            throw new \Exception($errorMessage);
        }

        # Parsing the response
        if ($searchItemsResponse->getErrors() !== null) {
            $errorMessage = "\nErrors returned from PAA5 API searchItems:\n";
            foreach ($searchItemsResponse->getErrors() as $errorObject) {
                $errorMessage .= "\nError #{$errorObject->getCode()}: {$errorObject->getMessage()}";
            }
            throw new \Exception($errorMessage);
        }

        return $searchItemsResponse;
    }

    /**
     * @param string $titleKeywords
     * @param string $textKeywords
     * @param array $resources
     * @param array $extraParams
     * @param bool $returnItemsInsteadOfResponse
     * @return Item[]|SearchItemsResponse
     * @throws \Exception
     */
    protected function searchItems($titleKeywords=null, $textKeywords=null, $resources=null, $extraParams=[], $returnItemsInsteadOfResponse=false) {
        if (empty($titleKeywords) && empty($textKeywords)) {
            throw new \Exception("Must provide at least one title or keyword search term");
        }

        $ps = SearchItemsParameterSet::generateParameterSetNewOnly();
        $ps->mergeParameters($extraParams);

        $searchItemsRequest = new SearchItemsRequest($ps->getParameters());
        $searchItemsRequest->setTitle($titleKeywords);
        $searchItemsRequest->setKeywords($textKeywords);
        $searchItemsRequest->setResources($resources);
        $searchItemsRequest->setPartnerTag($this->configuration->getPartnerTag());
        $searchItemsRequest->setMarketplace($this->configuration->getMarketplace());

        $searchItemsResponse = $this->validateAndCallSearchItems($searchItemsRequest);

        if ($returnItemsInsteadOfResponse) {
            $mappedResponse = [];
            if ($searchItemsResponse->getSearchResult() !== null) {
                if ($searchItemsResponse->getSearchResult()->getItems() !== null) {
                    foreach ($searchItemsResponse->getSearchResult()->getItems() as $item) {
                        $mappedResponse[$item->getASIN()] = new Item($item);
                    }
                }
            }
            return $mappedResponse;
        }

        return $searchItemsResponse;
    }

    /**
     * @param string|array $itemIds
     * @param array $resources
     * @param array $extraParams
     * @return Item[]|GetItemsResponse
     * @throws \Exception
     */
    public function getItemsAndReturnResponse($itemIds, $resources=null, $extraParams=[]) {
        return $this->getItems($itemIds, $resources, $extraParams, false);
    }
    /**
     * @param string|array $itemIds
     * @param array $resources
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function getItemsAndReturnAsinMappedItems($itemIds, $resources=null, $extraParams=[]) {
        return $this->getItems($itemIds, $resources, $extraParams, true);
    }

    /**
     * Looks up the given itemIds and returns the full set of Resources
     *
     * @param $itemIds
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function callGetItemsFull($itemIds, $extraParams=[]) {
        $resources = [
            GetItemsResource::BROWSE_NODE_INFOBROWSE_NODES,
            GetItemsResource::BROWSE_NODE_INFOBROWSE_NODESANCESTOR,
            GetItemsResource::BROWSE_NODE_INFOBROWSE_NODESSALES_RANK,
            GetItemsResource::BROWSE_NODE_INFOWEBSITE_SALES_RANK,
            GetItemsResource::CUSTOMER_REVIEWSCOUNT,
            GetItemsResource::CUSTOMER_REVIEWSSTAR_RATING,
//            GetItemsResource::IMAGESPRIMARYSMALL,
//            GetItemsResource::IMAGESPRIMARYMEDIUM,
            GetItemsResource::IMAGESPRIMARYLARGE,
//            GetItemsResource::IMAGESVARIANTSSMALL,
//            GetItemsResource::IMAGESVARIANTSMEDIUM,
            GetItemsResource::IMAGESVARIANTSLARGE,
            GetItemsResource::ITEM_INFOBY_LINE_INFO,
            GetItemsResource::ITEM_INFOCONTENT_INFO,
            GetItemsResource::ITEM_INFOCONTENT_RATING,
            GetItemsResource::ITEM_INFOCLASSIFICATIONS,
            GetItemsResource::ITEM_INFOEXTERNAL_IDS,
            GetItemsResource::ITEM_INFOFEATURES,
            GetItemsResource::ITEM_INFOMANUFACTURE_INFO,
            GetItemsResource::ITEM_INFOPRODUCT_INFO,
            GetItemsResource::ITEM_INFOTECHNICAL_INFO,
            GetItemsResource::ITEM_INFOTITLE,
            GetItemsResource::ITEM_INFOTRADE_IN_INFO,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYMAX_ORDER_QUANTITY,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYMESSAGE,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYMIN_ORDER_QUANTITY,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYTYPE,
            GetItemsResource::OFFERSLISTINGSCONDITION,
            GetItemsResource::OFFERSLISTINGSCONDITIONSUB_CONDITION,
            GetItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_AMAZON_FULFILLED,
            GetItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_FREE_SHIPPING_ELIGIBLE,
            GetItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_PRIME_ELIGIBLE,
            GetItemsResource::OFFERSLISTINGSDELIVERY_INFOSHIPPING_CHARGES,
            GetItemsResource::OFFERSLISTINGSIS_BUY_BOX_WINNER,
            GetItemsResource::OFFERSLISTINGSLOYALTY_POINTSPOINTS,
            GetItemsResource::OFFERSLISTINGSMERCHANT_INFO,
            GetItemsResource::OFFERSLISTINGSPRICE,
            GetItemsResource::OFFERSLISTINGSPROGRAM_ELIGIBILITYIS_PRIME_EXCLUSIVE,
            GetItemsResource::OFFERSLISTINGSPROGRAM_ELIGIBILITYIS_PRIME_PANTRY,
            GetItemsResource::OFFERSLISTINGSPROMOTIONS,
            GetItemsResource::OFFERSLISTINGSSAVING_BASIS,
            GetItemsResource::OFFERSSUMMARIESHIGHEST_PRICE,
            GetItemsResource::OFFERSSUMMARIESLOWEST_PRICE,
            GetItemsResource::OFFERSSUMMARIESOFFER_COUNT,
            GetItemsResource::PARENT_ASIN,
        ];

        return $this->getItemsAndReturnAsinMappedItems($itemIds, $resources, $extraParams);
    }

    /**
     * @param array $itemIds
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function callGetItemsAttributes($itemIds, $extraParams=[]) {
        $resources = [
            GetItemsResource::BROWSE_NODE_INFOWEBSITE_SALES_RANK,
            GetItemsResource::ITEM_INFOCONTENT_INFO,
            GetItemsResource::ITEM_INFOEXTERNAL_IDS,
            GetItemsResource::ITEM_INFOFEATURES,
            GetItemsResource::ITEM_INFOPRODUCT_INFO,
            GetItemsResource::ITEM_INFOTITLE,
        ];

        return $this->getItemsAndReturnAsinMappedItems($itemIds, $resources, $extraParams);
    }

    /**
     * Alias for callGetItemsFull()
     *
     * @deprecated - use callGetItemsFull() instead
     * @param $itemIds
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function callItemLookupFull($itemIds, $extraParams=[]) {
        return $this->callGetItemsFull($itemIds, $extraParams);
    }
    /**
     * Alias for callGetItemsFull()
     *
     * @deprecated - use callGetItemsAttributes() instead
     * @param $itemIds
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function callItemLookupAttributes($itemIds, $extraParams=[]) {
        return $this->callGetItemsAttributes($itemIds, $extraParams);
    }

    public function errorMessageIsAboutInvalidAsins($errorMsg) {
        $invalidAsins = [];
        $matches = [];
        $pattern = '/InvalidParameterValue: The ItemId ([0-9A-Z]+) provided in the request is invalid./';
        if (preg_match_all($pattern, $errorMsg, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $matchArray) {
                $invalidAsins[$matchArray[1]] = $matchArray[0];
            }
            return $invalidAsins;
        }
        return false;
    }

    /**
     * Looks up the given search keywords in the title and/or all text and return the full set of Resources
     *
     * @param string $titleKeywords
     * @param string $textKeywords
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function callSearchItemsFull($titleKeywords=null, $textKeywords=null, $extraParams=[]) {
        $resources = [
            SearchItemsResource::BROWSE_NODE_INFOBROWSE_NODES,
            SearchItemsResource::BROWSE_NODE_INFOBROWSE_NODESANCESTOR,
            SearchItemsResource::BROWSE_NODE_INFOBROWSE_NODESSALES_RANK,
            SearchItemsResource::BROWSE_NODE_INFOWEBSITE_SALES_RANK,
            SearchItemsResource::CUSTOMER_REVIEWSCOUNT,
            SearchItemsResource::CUSTOMER_REVIEWSSTAR_RATING,
//            SearchItemsResource::IMAGESPRIMARYSMALL,
//            SearchItemsResource::IMAGESPRIMARYMEDIUM,
            SearchItemsResource::IMAGESPRIMARYLARGE,
//            SearchItemsResource::IMAGESVARIANTSSMALL,
//            SearchItemsResource::IMAGESVARIANTSMEDIUM,
            SearchItemsResource::IMAGESVARIANTSLARGE,
            SearchItemsResource::ITEM_INFOBY_LINE_INFO,
            SearchItemsResource::ITEM_INFOCONTENT_INFO,
            SearchItemsResource::ITEM_INFOCONTENT_RATING,
            SearchItemsResource::ITEM_INFOCLASSIFICATIONS,
            SearchItemsResource::ITEM_INFOEXTERNAL_IDS,
            SearchItemsResource::ITEM_INFOFEATURES,
            SearchItemsResource::ITEM_INFOMANUFACTURE_INFO,
            SearchItemsResource::ITEM_INFOPRODUCT_INFO,
            SearchItemsResource::ITEM_INFOTECHNICAL_INFO,
            SearchItemsResource::ITEM_INFOTITLE,
            SearchItemsResource::ITEM_INFOTRADE_IN_INFO,
            SearchItemsResource::OFFERSLISTINGSAVAILABILITYMAX_ORDER_QUANTITY,
            SearchItemsResource::OFFERSLISTINGSAVAILABILITYMESSAGE,
            SearchItemsResource::OFFERSLISTINGSAVAILABILITYMIN_ORDER_QUANTITY,
            SearchItemsResource::OFFERSLISTINGSAVAILABILITYTYPE,
            SearchItemsResource::OFFERSLISTINGSCONDITION,
            SearchItemsResource::OFFERSLISTINGSCONDITIONSUB_CONDITION,
            SearchItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_AMAZON_FULFILLED,
            SearchItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_FREE_SHIPPING_ELIGIBLE,
            SearchItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_PRIME_ELIGIBLE,
            SearchItemsResource::OFFERSLISTINGSDELIVERY_INFOSHIPPING_CHARGES,
            SearchItemsResource::OFFERSLISTINGSIS_BUY_BOX_WINNER,
            SearchItemsResource::OFFERSLISTINGSLOYALTY_POINTSPOINTS,
            SearchItemsResource::OFFERSLISTINGSMERCHANT_INFO,
            SearchItemsResource::OFFERSLISTINGSPRICE,
            SearchItemsResource::OFFERSLISTINGSPROGRAM_ELIGIBILITYIS_PRIME_EXCLUSIVE,
            SearchItemsResource::OFFERSLISTINGSPROGRAM_ELIGIBILITYIS_PRIME_PANTRY,
            SearchItemsResource::OFFERSLISTINGSPROMOTIONS,
            SearchItemsResource::OFFERSLISTINGSSAVING_BASIS,
            SearchItemsResource::OFFERSSUMMARIESHIGHEST_PRICE,
            SearchItemsResource::OFFERSSUMMARIESLOWEST_PRICE,
            SearchItemsResource::OFFERSSUMMARIESOFFER_COUNT,
            SearchItemsResource::PARENT_ASIN,
            SearchItemsResource::SEARCH_REFINEMENTS,    // This is the only one not in GetItemsResource (K517)
        ];

        return $this->searchItemsAndReturnAsinMappedItems($titleKeywords, $textKeywords, $resources, $extraParams);
    }

    /**
     * @param string|array $itemIds
     * @param array $resources
     * @param array $extraParams
     * @return Item[]|GetItemsResponse
     * @throws \Exception
     */
    public function searchItemsAndReturnResponse($titleKeywords=null, $textKeywords=null, $resources=null, $extraParams=[]) {
        return $this->searchItems($titleKeywords, $textKeywords, $resources, $extraParams, false);
    }
    /**
     * @param string|array $itemIds
     * @param array $resources
     * @param array $extraParams
     * @return Item[]
     * @throws \Exception
     */
    public function searchItemsAndReturnAsinMappedItems($titleKeywords=null, $textKeywords=null, $resources=null, $extraParams=[]) {
        return $this->searchItems($titleKeywords, $textKeywords, $resources, $extraParams, true);
    }
}