<?php

namespace CaponicaAmazonPaa\ParameterSet;

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;

/**
 * Enumerated Parameters and options for PAA5.SearchItems() calls
 * Note: the PARAM_KEY_XYZ values start with a lower case letter so they match the labels in SearchItemsRequest
 */
class SearchItemsParameterSet
{
    protected $parameters = [];

    // Required Parameters
    const PARAM_KEY_PARTNER_TAG             = 'partnerTag';
    const PARAM_KEY_PARTNER_TYPE            = 'partnerType';        // Usually set to 'Associates'

    // Optional Parameters
    const PARAM_KEY_CONDITION               = 'condition';
    const PARAM_KEY_CURRENCY_PREF           = 'currencyOfPreference';   // e.g. 'USD', default based on Marketplace
    const PARAM_KEY_LANGUAGE_PREF           = 'languagesOfPreference';  // e.g. 'en_GB', default based on Marketplace
    const PARAM_KEY_MARKETPLACE             = 'marketplace';        // e.g. 'www.amazon.com'
    const PARAM_KEY_MERCHANT                = 'merchant';
    const PARAM_KEY_OFFER_COUNT             = 'offerCount';         // Integer between 1-10

    // Free search terms
    const PARAM_KEY_ACTOR                   = 'actor';
    const PARAM_KEY_ARTIST                  = 'artist';
    const PARAM_KEY_AUTHOR                  = 'author';
    const PARAM_KEY_BRAND                   = 'brand';
    const PARAM_KEY_KEYWORDS                = 'keywords';           // searches all product text?
    const PARAM_KEY_TITLE                   = 'title';              // searches only the product title?

    // Enumerated or numeric parameters
    const PARAM_KEY_AVAILABILITY            = 'availability';
    const PARAM_KEY_BROWSE_NODE_ID          = 'browseNodeId';
    const PARAM_KEY_DELIVERY_FLAGS          = 'deliveryFlags';
    const PARAM_KEY_ITEM_COUNT              = 'itemCount';
    const PARAM_KEY_ITEM_PAGE               = 'itemPage';
    const PARAM_KEY_MAX_PRICE               = 'maxPrice';
    const PARAM_KEY_MIN_PRICE               = 'minPrice';
    const PARAM_KEY_MIN_REVIEWS_RATING      = 'minReviewsRating';   // integer, e.g. use '1234' to mean '12.34'
    const PARAM_KEY_MIN_SAVING_PERCENT      = 'minSavingPercent';   // integer, e.g. use '1234' to mean '12.34'
    const PARAM_KEY_PROPERTIES              = 'properties';         // reserved for future use?
    const PARAM_KEY_RESOURCES               = 'resources';
    const PARAM_KEY_SEARCH_INDEX            = 'searchIndex';
    const PARAM_KEY_SORT_BY                 = 'sortBy';


    const PARAM_VALUE_AVAILABLE_IN_STOCK    = 'Available';
    const PARAM_VALUE_AVAILABLE_ANY         = 'IncludeOutOfStock';

    const PARAM_VALUE_CONDITION_ANY         = 'Any';
    const PARAM_VALUE_CONDITION_NEW         = 'New';                // Default = New
    const PARAM_VALUE_CONDITION_USED        = 'Used';
    const PARAM_VALUE_CONDITION_COLLECTIBLE = 'Collectible';
    const PARAM_VALUE_CONDITION_REFURBISHED = 'Refurbished';

    const PARAM_VALUE_DELIVERY_AMAZON_GLOBAL= 'AmazonGlobal';       // A delivery program featuring international shipping to certain Exportable Countries
    const PARAM_VALUE_DELIVERY_FREE_SHIPPING= 'FreeShipping';       // A delivery program featuring free shipping of an item
    const PARAM_VALUE_DELIVERY_FBA          = 'FulfilledByAmazon';  // Fulfilled by Amazon indicates that products are stored, packed and dispatched by Amazon
    const PARAM_VALUE_DELIVERY_PRIME        = 'Prime';  // An offer for an item which is eligible for Prime Program

    const PARAM_VALUE_MERCHANT_ALL          = 'All';                // Default = All
    const PARAM_VALUE_MERCHANT_AMAZON       = 'Amazon';             // Only return offers sold by Amazon Retail

    const PARAM_VALUE_SORT_BY_REVIEWS       = 'AvgCustomerReviews';
    const PARAM_VALUE_SORT_BY_FEATURED      = 'Featured';
    const PARAM_VALUE_SORT_BY_NEWEST        = 'NewestArrivals';
    const PARAM_VALUE_SORT_BY_PRICE_DESC    = 'Price:HighToLow';
    const PARAM_VALUE_SORT_BY_PRICE_ASC     = 'Price:LowToHigh';
    const PARAM_VALUE_SORT_BY_RELEVANCE     = 'Relevance';

    // RESOURCE options are enumerated in Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource
    // Resource option details are detailed here: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
    // Locale options (inc Currency, Language & Search Index) are detailed at https://webservices.amazon.com/paapi5/documentation/locale-reference.html

    public static function generateParameterSetNewOnly($numOffers=10) {
        $ps = new SearchItemsParameterSet();
        $ps->setParameters([
            self::PARAM_KEY_CONDITION       => self::PARAM_VALUE_CONDITION_NEW,
            self::PARAM_KEY_OFFER_COUNT     => $numOffers,
            self::PARAM_KEY_PARTNER_TYPE    => PartnerType::ASSOCIATES,
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