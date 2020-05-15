<?php

namespace CaponicaAmazonPaa\ParameterSet;

use CaponicaAmazonPaa\Client\ApaaClientConfiguration;

/**
 * A generic set of parameters for a PAA request
 */
class GenericParameterSet
{
    const PARAM_KEY_ACCESS_KEY              = 'AWSAccessKeyId';
    const PARAM_KEY_PARTNER_TAG             = 'PartnerTag';
    const PARAM_KEY_OPERATION               = 'Operation';
    const PARAM_KEY_SERVICE                 = 'Service';
    const PARAM_KEY_TIMESTAMP               = 'Timestamp';

    const PARAM_VALUE_SERVICE               = 'AWSECommerceService';

    const PARAM_VALUE_OPERATION_ITEM_LOOKUP = 'ItemLookup';
    const PARAM_VALUE_OPERATION_ITEM_SEARCH = 'ItemSearch';

    protected $parameters = [];

    public function addParameter($key, $value) {
        $this->parameters[$key] = $value;
    }
    public function addParameters($parameters) {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    public function generateSignedUrlForConfiguration(ApaaClientConfiguration $configuration) {
        if (empty($this->parameters[self::PARAM_KEY_TIMESTAMP])) {
            $this->parameters[self::PARAM_KEY_TIMESTAMP] = gmdate('Y-m-d\TH:i:s\Z');
        }
        $this->addParameter(self::PARAM_KEY_ACCESS_KEY,     $configuration->getAccessKey());
        $this->addParameter(self::PARAM_KEY_PARTNER_TAG,    $configuration->getPartnerTag());
        $this->addParameter(self::PARAM_KEY_SERVICE,        self::PARAM_VALUE_SERVICE);

        ksort($this->parameters);
        $parameterString = '';
        foreach ($this->parameters as $key => $value) {
            if (!empty($parameterString)) {
                $parameterString .= '&';
            }
            $parameterString .= rawurlencode($key) . '=' . rawurlencode($value);
        }

        $stringToSign = "GET\n{$configuration->getDomain()}\n{$configuration->getUri()}\n$parameterString";

        $signature = base64_encode(hash_hmac("sha256", $stringToSign, $configuration->getSecretAccessKey(), true));

        $signedUrl = $configuration->getEndpoint() . '?' . $parameterString . '&Signature=' . rawurlencode($signature);

        return $signedUrl;
    }
}