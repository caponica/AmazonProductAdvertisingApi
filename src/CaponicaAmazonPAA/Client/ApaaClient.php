<?php

namespace CaponicaAmazonPAA\Client;

use CaponicaAmazonPAA\ParameterSet\GenericParameterSet;
use CaponicaAmazonPAA\ParameterSet\ItemLookupParameterSet;

/**
 * Client to connect to the Amazon Product Advertising API
 */
class ApaaClient
{
    /**
     * @var ApaaClientConfiguration
     */
    private $configuration;

    public function __construct(ApaaClientConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function callItemLookupLarge($itemId) {
        $parameters = new ItemLookupParameterSet($itemId, [
            ItemLookupParameterSet::PARAM_KEY_RESPONSE_GROUP => ItemLookupParameterSet::PARAM_VALUE_RESPONSE_GROUP_LARGE,
        ]);
        return $this->makeApiCall($parameters);
    }
    public function callItemLookup(GenericParameterSet $parameters) {
        $parameters->addParameter(GenericParameterSet::PARAM_KEY_OPERATION, GenericParameterSet::PARAM_VALUE_OPERATION_ITEM_LOOKUP);
        return $this->makeApiCall($parameters);
    }

    public function callItemSearch(GenericParameterSet $parameters) {
        $parameters->addParameter(GenericParameterSet::PARAM_KEY_OPERATION, GenericParameterSet::PARAM_VALUE_OPERATION_ITEM_SEARCH);
        return $this->makeApiCall($parameters);
    }

    private function makeApiCall(GenericParameterSet $parameters) {
        $url = $parameters->generateSignedUrlForConfiguration($this->configuration);
//echo "\n<p>URL: $url </p>";

        $curl = \curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));


        try {
            $success = 1;
            $response = curl_exec($curl);
        } catch (\Exception $e) {
            $success = 0;
            $response = $e->getMessage();
        }

//var_dump($response);

        curl_close($curl);

        return [
            'response'  => $response,
            'success'   => $success,
        ];
    }
}