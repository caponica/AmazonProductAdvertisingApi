<?php

namespace CaponicaAmazonPAA\Client;

use CaponicaAmazonPAA\ParameterSet\GenericParameterSet;
use CaponicaAmazonPAA\ParameterSet\ItemLookupParameterSet;
use CaponicaAmazonPAA\Response\ItemLookupResponse;

/**
 * Client to connect to the Amazon Product Advertising API
 */
class ApaaClient
{
    /**
     * @var ApaaClientConfiguration
     */
    private $configuration;

    public function __construct(ApaaClientConfiguration $configuration) {
        $this->configuration = $configuration;
    }

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
    public function callItemLookupLarge($itemId) {
        $parameters = new ItemLookupParameterSet($itemId, [
            ItemLookupParameterSet::PARAM_KEY_RESPONSE_GROUP => ItemLookupParameterSet::PARAM_VALUE_RESPONSE_GROUP_LARGE,
        ]);
        return $this->callItemLookupAndReturnObject($parameters);
    }
    public function callItemLookupFull($itemId) {
        $parameters = new ItemLookupParameterSet($itemId, [
            ItemLookupParameterSet::PARAM_KEY_RESPONSE_GROUP => implode(',',[
                ItemLookupParameterSet::PARAM_VALUE_RESPONSE_GROUP_LARGE,
                ItemLookupParameterSet::PARAM_VALUE_RESPONSE_GROUP_OFFER_FULL,
            ]),
        ]);
        return $this->callItemLookupAndReturnObject($parameters);
    }
}