<?php

namespace CaponicaAmazonPaa\Client;

/**
 * Client to connect to the Amazon Product Advertising API
 */
class ApaaClientConfiguration
{
    private $domainSuffix;
    private $accessKeyId;
    private $secretAccessKey;
    private $associateTag;

    const SITE_CANADA   = 'CA';
    const SITE_MEXICO   = 'MX';
    const SITE_USA      = 'US';
    const SITE_GERMANY  = 'DE';
    const SITE_SPAIN    = 'ES';
    const SITE_FRANCE   = 'FR';
    const SITE_ITALY    = 'IT';
    const SITE_UK       = 'UK';
    const SITE_CHINA    = 'CN';
    const SITE_INDIA    = 'IN';
    const SITE_JAPAN    = 'JP';

    public static function getDomainSuffixes() {
        return [
            self::SITE_CANADA   => 'ca',
            self::SITE_MEXICO   => 'com.mx',
            self::SITE_USA      => 'com',
            self::SITE_GERMANY  => 'de',
            self::SITE_SPAIN    => 'es',
            self::SITE_FRANCE   => 'fr',
            self::SITE_ITALY    => 'it',
            self::SITE_UK       => 'co.uk',
            self::SITE_CHINA    => 'cn',
            self::SITE_INDIA    => 'in',
            self::SITE_JAPAN    => 'co.jp',
        ];
    }

    public static function buildFromArray($configArray=[], $domainSuffix=null) {
        $requiredFields = [ 'access_key', 'secret_key', 'associate_tag' ];
        foreach ($requiredFields as $requiredField) {
            if (empty($configArray[$requiredField])) {
                throw new \InvalidArgumentException('Missing ApaaClientConfiguration key ' . $requiredField);
            }
        }

        if (!empty($domainSuffix)) {
            $configArray['domain_suffix'] = $domainSuffix;
        } elseif (empty($configArray['domain_suffix'])) {
            $configArray['domain_suffix'] = 'com';
        }

        return new ApaaClientConfiguration(
            $configArray['access_key'],
            $configArray['secret_key'],
            $configArray['associate_tag'],
            $configArray['domain_suffix']
        );
    }

    public function __construct($accessKeyId, $secretAccessKey, $associateTag, $domainSuffix = 'com')
    {
        if (!in_array($domainSuffix, self::getDomainSuffixes())) {
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
    public function getDomainSuffix() {
        return $this->domainSuffix;
    }
    public function setDomainSuffix($suffix) {
        $this->domainSuffix = $suffix;
    }

    public function getEndpoint() {
        return $this->getProtocol() . $this->getDomain() . $this->getUri();
    }

    /**
     * Gets the countryCode for this client's Amazon site (which will be one of the SITE_XXX values)
     *
     * @return null|string
     */
    public function getCountryCode() {
        $mapDomainSuffixToCountryCode = array_flip(self::getDomainSuffixes());
        if (!empty($mapDomainSuffixToCountryCode[$this->domainSuffix])) {
            return $mapDomainSuffixToCountryCode[$this->domainSuffix];
        }
        return null;
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