<?php

namespace CaponicaAmazonPAA\ParameterSet;

/**
 * A generic set of parameters for a PAA request
 */
class ItemLookupParameterSet extends GenericParameterSet
{
    // Required Parameters
    const PARAM_KEY_ITEM_ID                 = 'ItemId';             // search string

    // Optional Parameters
    const PARAM_KEY_CONDITION               = 'Condition';
    const PARAM_KEY_ID_TYPE                 = 'IdType';
    const PARAM_KEY_INCLUDE_REVIEWS         = 'IncludeReviewsSummary';  // True or False
    const PARAM_KEY_MERCHANT_ID             = 'MerchantId';         // Can filter to Amazon only
    const PARAM_KEY_RELATED_ITEM_PAGE       = 'RelatedItemPage';    // Integer
    const PARAM_KEY_RELATIONSHIP_TYPE       = 'RelationshipType';   // See http://docs.aws.amazon.com/AWSECommerceService/latest/DG/Motivating_RelatedItems.html#RelationshipTypes
    const PARAM_KEY_SEARCH_INDEX            = 'SearchIndex';        // See http://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html
    const PARAM_KEY_TRUNCATE_REVIEWS_AT     = 'TruncateReviewsAt';  // Integer number of characters. 0 means 'do not truncate'.
    const PARAM_KEY_VARIATIONS_PAGE         = 'VariationPage';      // Integer between 1-150
    const PARAM_KEY_RESPONSE_GROUP          = 'ResponseGroup';      // CSL of PARAM_VALUE_RESPONSE_GROUP_XYZ values

    const PARAM_VALUE_CONDITION_NEW         = 'New';                // Default = New
    const PARAM_VALUE_CONDITION_USED        = 'Used';
    const PARAM_VALUE_CONDITION_COLLECTIBLE = 'Collectible';
    const PARAM_VALUE_CONDITION_REFURBISHED = 'Refurbished';
    const PARAM_VALUE_CONDITION_ALL         = 'All';

    const PARAM_VALUE_ID_TYPE_ASIN          = 'ASIN';               // Default = ASIN
    const PARAM_VALUE_ID_TYPE_EAN           = 'EAN';
    const PARAM_VALUE_ID_TYPE_ISBN          = 'ISBN';
    const PARAM_VALUE_ID_TYPE_SKU           = 'SKU';
    const PARAM_VALUE_ID_TYPE_UPC           = 'UPC';

    const PARAM_VALUE_MERCHANT_AMAZON       = 'Amazon';

                                                                              // |M|L|
    const PARAM_VALUE_RESPONSE_GROUP_ACCESSORIES        = 'Accessories';      // | |x|
    const PARAM_VALUE_RESPONSE_GROUP_BROWSE_NODES       = 'BrowseNodes';      // | |x|
    const PARAM_VALUE_RESPONSE_GROUP_EDITORIAL_REVIEW   = 'EditorialReview';  // |x|x|
    const PARAM_VALUE_RESPONSE_GROUP_IMAGES             = 'Images';           // |x|x|
    const PARAM_VALUE_RESPONSE_GROUP_ITEM_ATTRIBUTES    = 'ItemAttributes';   // |x|x|
    const PARAM_VALUE_RESPONSE_GROUP_ITEM_IDS           = 'ItemIds';          // | | |
    const PARAM_VALUE_RESPONSE_GROUP_LARGE              = 'Large';            // | |=|  // Includes: Medium + Accessories, BrowseNodes, Offers, Reviews, Similarities, Tracks
    const PARAM_VALUE_RESPONSE_GROUP_MEDIUM             = 'Medium';           // |=|x|  // Includes: EditorialReview, Images, ItemAttributes, OfferSummary, Request, SalesRank, Small
    const PARAM_VALUE_RESPONSE_GROUP_OFFER_FULL         = 'OfferFull';        // | | |
    const PARAM_VALUE_RESPONSE_GROUP_OFFERS             = 'Offers';           // | |x|
    const PARAM_VALUE_RESPONSE_GROUP_OFFER_SUMMARY      = 'OfferSummary';     // |x|x|
    const PARAM_VALUE_RESPONSE_GROUP_PROMOTION_SUMMARY  = 'PromotionSummary'; // | | |
    const PARAM_VALUE_RESPONSE_GROUP_RELATED_ITEMS      = 'RelatedItems';     // | | |
    const PARAM_VALUE_RESPONSE_GROUP_REVIEWS            = 'Reviews';          // | |x|
    const PARAM_VALUE_RESPONSE_GROUP_SALES_RANK         = 'SalesRank';        // |x|x|
    const PARAM_VALUE_RESPONSE_GROUP_SMALL              = 'Small';            // |x|x|  // Returns the item's ASIN, title, product group, and author.
    const PARAM_VALUE_RESPONSE_GROUP_SIMILARITIES       = 'Similarities';     // | |x|
    const PARAM_VALUE_RESPONSE_GROUP_TRACKS             = 'Tracks';           // | |x|
    const PARAM_VALUE_RESPONSE_GROUP_VARIATION_IMAGES   = 'VariationImages';  // | | |
    const PARAM_VALUE_RESPONSE_GROUP_VARIATIONS         = 'Variations';       // | | |
    const PARAM_VALUE_RESPONSE_GROUP_VARIATION_SUMMARY  = 'VariationSummary'; // | | |

    const PARAM_VALUE_FALSE                 = 'False';
    const PARAM_VALUE_TRUE                  = 'True';

    public function __construct($itemId, $parameters=[])
    {
        $this->parameters = $parameters;
        $this->addParameter(self::PARAM_KEY_ITEM_ID, $itemId);
        $this->addParameter(self::PARAM_KEY_OPERATION, self::PARAM_VALUE_OPERATION_ITEM_LOOKUP);
    }


}