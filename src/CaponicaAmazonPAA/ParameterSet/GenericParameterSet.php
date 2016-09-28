<?php

namespace CaponicaAmazonPAA\ParameterSet;

use CaponicaAmazonPAA\Client\ApaaClientConfiguration;

/**
 * A generic set of parameters for a PAA request
 */
class GenericParameterSet
{
    const PARAM_KEY_ACCESS_KEY      = 'AWSAccessKeyId';
    const PARAM_KEY_ASSOCIATE_TAG   = 'AssociateTag';
    const PARAM_KEY_OPERATION       = 'Operation';
    const PARAM_KEY_SERVICE         = 'Service';
    const PARAM_KEY_TIMESTAMP       = 'Timestamp';

    const PARAM_VALUE_SERVICE       = 'AWSECommerceService';

    private $parameters = [];

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
        $this->addParameter(self::PARAM_KEY_ASSOCIATE_TAG,  $configuration->getAssociateTag());
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