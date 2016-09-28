<?php

namespace CaponicaAmazonPAA\Client;

/**
 * Client to connect to the Amazon Product Advertising API
 */
class ApaaClientConfiguration
{
    private $domainSuffix;
    private $accessKeyId;
    private $secretAccessKey;
    private $associateTag;

    public function __construct($accessKeyId, $secretAccessKey, $associateTag, $domainSuffix = 'com')
    {
        if (!in_array($domainSuffix, ['com','co.uk','de','es','fr','it','ca','com.br','com.mx','cn','co.jp','in'])) {
            throw new \InvalidArgumentException('Unknown Amazon domain suffix provided');
        }

        $this->domainSuffix     = $domainSuffix;
        $this->accessKeyId      = $accessKeyId;
        $this->secretAccessKey  = $secretAccessKey;
        $this->associateTag     = $associateTag;
    }

    public function getAccessKey() {
        return $this->accessKeyId;
    }
    public function getSecretAccessKey() {
        return $this->secretAccessKey;
    }
    public function getAssociateTag() {
        return $this->associateTag;
    }

    public function getEndpoint() {
        return $this->getProtocol() . $this->getDomain() . $this->getUri();
    }

    private function getProtocol() {
        return 'http://';
    }
    public function getDomain() {
        return 'webservices.amazon.' . $this->domainSuffix;
    }
    public function getUri() {
        return '/onca/xml';
    }
}