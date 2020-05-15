<?php

namespace CaponicaAmazonPaa\Client;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use CaponicaAmazonPaa\ParameterSet\GenericParameterSet;
use CaponicaAmazonPaa\ParameterSet\ItemLookupParameterSet;
use CaponicaAmazonPaa\Response\ItemLookupResponse;

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

    public function getItems($itemIds, $resources=null, $extraParams=[]) {
        $getItemsRequest = new GetItemsRequest($extraParams);
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setResources($resources);
        $getItemsRequest->setPartnerTag($this->configuration->getPartnerTag());
        $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);

        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            $errorMessage = "Error forming PAA.getItems() request" . PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                $errorMessage .= $invalidProperty . PHP_EOL;
            }
            throw new \InvalidArgumentException($errorMessage);
        }

        # Sending the request
        try {
            $getItemsResponse = $this->apiInstance->getItems($getItemsRequest);

echo 'PAA called successfully', PHP_EOL;
echo 'Complete Response: ', $getItemsResponse, PHP_EOL;

            # Parsing the response
            if ($getItemsResponse->getItemsResult() !== null) {
echo 'Printing all item information in ItemsResult:', PHP_EOL;
                if ($getItemsResponse->getItemsResult()->getItems() !== null) {
                    $mappedResponse = [];
                    foreach ($getItemsResponse->getItemsResult()->getItems() as $item) {
                        $mappedResponse[$item->getASIN()] = $item;
                    }
                    $responseList = $mappedResponse;

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
        } catch (\Exception $exception) {
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        }
    }

### OLD CODE BELOW HERE ###

    private function makeApiCall(GenericParameterSet $parameters) {
        $url = $parameters->generateSignedUrlForConfiguration($this->configuration);

        $curl = \curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    // plain calls to the API, which return the XML response
    public function callItemLookupAndReturnXml(GenericParameterSet $parameters) {
        $parameters->addParameter(GenericParameterSet::PARAM_KEY_OPERATION, GenericParameterSet::PARAM_VALUE_OPERATION_ITEM_LOOKUP);
        return $this->makeApiCall($parameters);
    }
    public function callItemSearchAndReturnXml(GenericParameterSet $parameters) {
        $parameters->addParameter(GenericParameterSet::PARAM_KEY_OPERATION, GenericParameterSet::PARAM_VALUE_OPERATION_ITEM_SEARCH);
        return $this->makeApiCall($parameters);
    }

    // wrapped calls to the API, which return Response objects
    public function callItemLookupAndReturnObject(GenericParameterSet $parameters) {
        $apiResponse = $this->callItemLookupAndReturnXml($parameters);
        return new ItemLookupResponse($apiResponse);
    }

    // convenience methods for common calls
    public function callItemLookupAttributes($itemId) {
        $parameters = new ItemLookupParameterSet($itemId, [
            ItemLookupParameterSet::PARAM_KEY_RESPONSE_GROUP => ItemLookupParameterSet::PARAM_VALUE_RESPONSE_GROUP_ITEM_ATTRIBUTES,
        ]);
        return $this->callItemLookupAndReturnObject($parameters);
    }
    public function callItemLookupLarge($itemId) {
        $parameters = new ItemLookupParameterSet($itemId, [
            ItemLookupParameterSet::PARAM_KEY_RESPONSE_GROUP => ItemLookupParameterSet::PARAM_VALUE_RESPONSE_GROUP_LARGE,
        ]);
        return $this->callItemLookupAndReturnObject($parameters);
    }
    public function callItemLookupFull($itemIds) {
        $resources = [
            GetItemsResource::BROWSE_NODE_INFOWEBSITE_SALES_RANK,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYMIN_ORDER_QUANTITY,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYMAX_ORDER_QUANTITY,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYMESSAGE,
            GetItemsResource::OFFERSLISTINGSAVAILABILITYTYPE,
            GetItemsResource::OFFERSLISTINGSIS_BUY_BOX_WINNER,
            GetItemsResource::OFFERSLISTINGSCONDITION,
            GetItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_AMAZON_FULFILLED,
            GetItemsResource::OFFERSLISTINGSDELIVERY_INFOIS_PRIME_ELIGIBLE,
            GetItemsResource::OFFERSLISTINGSMERCHANT_INFO,
            GetItemsResource::OFFERSLISTINGSPRICE,
            GetItemsResource::OFFERSLISTINGSPROGRAM_ELIGIBILITYIS_PRIME_EXCLUSIVE,
            GetItemsResource::OFFERSLISTINGSPROMOTIONS,
            GetItemsResource::OFFERSSUMMARIESOFFER_COUNT,
            GetItemsResource::OFFERSSUMMARIESLOWEST_PRICE,
            GetItemsResource::OFFERSSUMMARIESHIGHEST_PRICE,
        ];
        $this->getItems($itemIds, $resources);

        //        return $this->callItemLookupAndReturnObject($parameters);
    }
}