<?php

namespace CaponicaAmazonPaa\ParameterSet;

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;

/**
 * Enumerated Parameters and options for PAA5.GetItems() calls
 * Note: the PARAM_KEY_XYZ values start with a lower case letter so they match the labels in GetItemsRequest
 */
class GetItemsParameterSet
{
    protected $parameters = [];

    // Required Parameters
    const PARAM_KEY_ITEM_IDS                = 'itemIds';            // array of ASINs to search for
    const PARAM_KEY_PARTNER_TAG             = 'partnerTag';
    const PARAM_KEY_PARTNER_TYPE            = 'partnerType';        // Usually set to 'Associates'

    // Optional Parameters
    const PARAM_KEY_CONDITION               = 'condition';
    const PARAM_KEY_CURRENCY_PREF           = 'currencyOfPreference';   // e.g. 'USD', default based on Marketplace
    const PARAM_KEY_LANGUAGE_PREF           = 'languagesOfPreference';  // e.g. 'en_GB', default based on Marketplace
    const PARAM_KEY_ITEM_ID_TYPE            = 'itemIdType';
    const PARAM_KEY_MARKETPLACE             = 'marketplace';        // e.g. 'www.amazon.com'
    const PARAM_KEY_MERCHANT                = 'merchant';
    const PARAM_KEY_OFFER_COUNT             = 'offerCount';         // Integer between 1-10
    const PARAM_KEY_RESOURCES               = 'resources';

    const PARAM_VALUE_CONDITION_ANY         = 'Any';
    const PARAM_VALUE_CONDITION_NEW         = 'New';                // Default = New
    const PARAM_VALUE_CONDITION_USED        = 'Used';
    const PARAM_VALUE_CONDITION_COLLECTIBLE = 'Collectible';
    const PARAM_VALUE_CONDITION_REFURBISHED = 'Refurbished';

    const PARAM_VALUE_ID_TYPE_ASIN          = 'ASIN';               // Default = ASIN (no other options seem acceptable at the moment)

    const PARAM_VALUE_MERCHANT_ALL          = 'All';                // Default = All
    const PARAM_VALUE_MERCHANT_AMAZON       = 'Amazon';             // Only return offers sold by Amazon Retail

    // RESOURCE options are enumerated in Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource
    // Resource option details are detailed here: https://webservices.amazon.com/paapi5/documentation/get-items.html#resources-parameter
    // Locale options (inc Currency, Language & Search Index) are detailed at https://webservices.amazon.com/paapi5/documentation/locale-reference.html

    const PARAM_VALUE_FALSE                 = 'False';
    const PARAM_VALUE_TRUE                  = 'True';

    public static function generateParameterSetNewOnly($numOffers=10) {
        $ps = new GetItemsParameterSet();
        $ps->setParameters([
            self::PARAM_KEY_CONDITION       => self::PARAM_VALUE_CONDITION_NEW,
            self::PARAM_KEY_OFFER_COUNT     => $numOffers,
            self::PARAM_KEY_PARTNER_TYPE    => PartnerType::ASSOCIATES,
            self::PARAM_KEY_ITEM_ID_TYPE    => self::PARAM_VALUE_ID_TYPE_ASIN,
        ]);
        return $ps;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }
    public function getParameters() {
        return $this->parameters;
    }

    public function mergeParameters($parameters) {
        array_merge($this->parameters, $parameters);
    }
}