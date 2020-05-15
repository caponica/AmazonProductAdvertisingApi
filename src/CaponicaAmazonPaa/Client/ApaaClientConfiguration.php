<?php

namespace CaponicaAmazonPaa\Client;

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\Configuration;

/**
 * Client to connect to the Amazon Product Advertising API
 */
class ApaaClientConfiguration
{
    private $accessKeyId;
    private $secretAccessKey;
    private $partnerTag;
    private $domainSuffix;
    private $region;
    private $countryCode;

    const SITE_BRAZIL       = 'BR';
    const SITE_CANADA       = 'CA';
    const SITE_MEXICO       = 'MX';
    const SITE_USA          = 'US';

    const SITE_FRANCE       = 'FR';
    const SITE_GERMANY      = 'DE';
    const SITE_ITALY        = 'IT';
    const SITE_NETHERLANDS  = 'NL';
    const SITE_SPAIN        = 'ES';
    const SITE_TURKEY       = 'TR';
    const SITE_UK           = 'UK';

    const SITE_AUSTRALIA    = 'AU';
    const SITE_INDIA        = 'IN';
    const SITE_JAPAN        = 'JP';
    const SITE_SINGAPORE    = 'SG';
    const SITE_UAE          = 'AE';

    public static function getDomainSuffixes() {
        return [
            self::SITE_BRAZIL       => 'com.br',
            self::SITE_CANADA       => 'ca',
            self::SITE_MEXICO       => 'com.mx',
            self::SITE_USA          => 'com',

            self::SITE_FRANCE       => 'fr',
            self::SITE_GERMANY      => 'de',
            self::SITE_ITALY        => 'it',
            self::SITE_NETHERLANDS  => 'nl',
            self::SITE_SPAIN        => 'es',
            self::SITE_TURKEY       => 'com.tr',
            self::SITE_UK           => 'co.uk',

            self::SITE_INDIA        => 'in',
            self::SITE_UAE          => 'ae',

            self::SITE_AUSTRALIA    => 'com.au',
            self::SITE_JAPAN        => 'co.jp',
            self::SITE_SINGAPORE    => 'sg',
        ];
    }
    public static function getRegions() {
        return [
            self::SITE_BRAZIL       => 'us-east-1',
            self::SITE_CANADA       => 'us-east-1',
            self::SITE_MEXICO       => 'us-east-1',
            self::SITE_USA          => 'us-east-1',

            self::SITE_FRANCE       => 'eu-west-1',
            self::SITE_GERMANY      => 'eu-west-1',
            self::SITE_ITALY        => 'eu-west-1',
            self::SITE_NETHERLANDS  => 'eu-west-1',
            self::SITE_SPAIN        => 'eu-west-1',
            self::SITE_TURKEY       => 'eu-west-1',
            self::SITE_UK           => 'eu-west-1',

            self::SITE_INDIA        => 'eu-west-1',
            self::SITE_UAE          => 'eu-west-1',

            self::SITE_AUSTRALIA    => 'us-west-2',
            self::SITE_JAPAN        => 'us-west-2',
            self::SITE_SINGAPORE    => 'us-west-2',
        ];
    }

    /**
     * @param array $configArray
     * @param null $domainSuffix
     * @return ApaaClientConfiguration
     *
     * @deprecated - use buildFromArrayAndMarketplace instead
     */
    public static function buildFromArray($configArray=[], $domainSuffix=null) {
        $mapDomainSuffixToCountryCode = array_flip(self::getDomainSuffixes());

        $countryCode = null;
        if (!empty($domainSuffix) && !empty($mapDomainSuffixToCountryCode[$domainSuffix])) {
            $countryCode = $mapDomainSuffixToCountryCode[$domainSuffix];
        }

        return self::buildFromArrayAndCountry($configArray, $countryCode);
    }
    public static function buildFromArrayAndCountry($configArray=[], $countryCode=null) {
        $requiredFields = [ 'access_key', 'secret_key', 'partner_tag' ];
        foreach ($requiredFields as $requiredField) {
            if (empty($configArray[$requiredField])) {
                throw new \InvalidArgumentException('Missing ApaaClientConfiguration key ' . $requiredField);
            }
        }

        if (!empty($countryCode)) {
            $configArray['country_code'] = $countryCode;
        } elseif (empty($configArray['country_code'])) {
            $configArray['country_code'] = self::SITE_USA;
        }

        return new ApaaClientConfiguration(
            $configArray['access_key'],
            $configArray['secret_key'],
            $configArray['partner_tag'],
            $configArray['country_code']
        );
    }

    public function __construct($accessKeyId, $secretAccessKey, $partnerTag, $countryCode)
    {
        $domainSuffix   = self::lookupDomainSuffix($countryCode);
        $region         = self::lookupRegion($countryCode);

        if (empty($domainSuffix) || empty($region)) {
            throw new \InvalidArgumentException('Unknown Amazon country code provided: ' . $countryCode);
        }

        $this->accessKeyId      = $accessKeyId;
        $this->secretAccessKey  = $secretAccessKey;
        $this->partnerTag       = $partnerTag;
        $this->countryCode      = $countryCode;
        $this->domainSuffix     = $domainSuffix;
        $this->region           = $region;
    }

    public function getAccessKey() {
        return $this->accessKeyId;
    }
    public function getSecretAccessKey() {
        return $this->secretAccessKey;
    }
    public function getPartnerTag() {
        return $this->partnerTag;
    }
    public function getCountryCode() {
        return $this->countryCode;
    }
    public function getDomainSuffix() {
        return $this->domainSuffix;
    }
    public function getRegion() {
        return $this->region;
    }

//    public function getEndpoint() {
//        return $this->getProtocol() . $this->getHost();
//    }

    public static function lookupDomainSuffix($countryCode) {
        $lookup = self::getDomainSuffixes();
        return !empty($lookup[$countryCode]) ? $lookup[$countryCode] : null;
    }
    public static function lookupRegion($countryCode) {
        $lookup = self::getRegions();
        return !empty($lookup[$countryCode]) ? $lookup[$countryCode] : null;
    }

    public function createApiInstance() {
        $config = new Configuration();

        $config->setAccessKey($this->getAccessKey());
        $config->setSecretKey($this->getSecretAccessKey());

        /*
         * PAAPI host and region to which you want to send request
         * For more details refer:
         * https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
         */
        $config->setHost($this->getHost());
        $config->setRegion($this->getRegion());

        $apiInstance = new DefaultApi(
            new GuzzleHttp\Client(),
            $config
        );

        return $apiInstance;
    }

//    private function getProtocol() {
//        return 'http://';
//    }
    public function getHost() {
        return 'webservices.amazon.' . $this->domainSuffix;
    }
}