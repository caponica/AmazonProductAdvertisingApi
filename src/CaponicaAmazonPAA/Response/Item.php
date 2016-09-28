<?php

namespace CaponicaAmazonPAA\Response;

/**
 * Represents an item returned by a PAA response
 */
class Item
{
    const ITEM_LINK_NAME_ADD_TO_WISHLIST  = 'Add To Wishlist';
    const ITEM_LINK_NAME_TELL_A_FRIEND    = 'Tell A Friend';
    const ITEM_LINK_NAME_CUSTOMER_REVIEWS = 'All Customer Reviews';
    const ITEM_LINK_NAME_IFRAME_REVIEWS   = 'iFrame Reviews';
    const ITEM_LINK_NAME_ALL_OFFERS       = 'All Offers';

    /** @var string */
    private $asin;
    /** @var string */
    private $detailPageUrl;
    /** @var array */
    private $itemLinks = [];
    /** @var int */
    private $salesRank;
    /** @var Image */
    private $largeImage;
    /** @var ItemAttributes */
    private $itemAttributes;
    private $offerSummary;  // @todo (it's not very useful since LowestNewPrice given excludes postage)
    /** @var OfferCollection */
    private $offers;
    /** @var bool */
    private $hasCustomerReviews;
    /** @var string */
    private $editorialSource;
    /** @var string */
    private $editorialContent;
    /** @var bool */
    private $editorialIsLinkSuppressed;
    /** @var SimilarProduct[] */
    private $similarProducts = [];
    /** @var BrowseNode[]  */
    private $browseNodes = [];

    public function __construct(\SimpleXMLElement $source) {
        if ($source->ASIN) {
            $this->asin = (string)$source->ASIN;
        }
        if ($source->DetailPageURL) {
            $this->detailPageUrl = (string)$source->DetailPageURL;
        }
        if ($source->ItemLinks && $source->ItemLinks->ItemLink) {
            foreach ($source->ItemLinks->ItemLink as $itemLink) {
                $name  = (string)$itemLink['Description'];
                $value = (string)$itemLink['URL'];
                $this->itemLinks[$name] = $value;
            }
        }
        if ($source->SalesRank) {
            $this->salesRank = (int)$source->SalesRank;
        }
        if ($source->LargeImage) {
            $this->largeImage = new Image($source->LargeImage);
        }
        if ($source->ItemAttributes) {
            $this->itemAttributes = new ItemAttributes($source->ItemAttributes);
        }
        if ($source->Offers) {
            $this->offers = new OfferCollection($source->Offers);
        }
        if ($source->CustomerReviews && $source->CustomerReviews->IFrameURL) {
            $this->itemLinks[self::ITEM_LINK_NAME_IFRAME_REVIEWS] = (string)$source->CustomerReviews->IFrameURL;
        }
        if ($source->CustomerReviews && $source->CustomerReviews->HasReviews) {
            $this->hasCustomerReviews = 'true'==(string)$source->CustomerReviews->HasReviews;
        }
        if ($source->EditorialReviews && $source->EditorialReviews->EditorialReview) {
            $editorialReview = $source->EditorialReviews->EditorialReview;
            if ($editorialReview->Source) {
                $this->editorialSource = $editorialReview->Source;
            }
            if ($editorialReview->Content) {
                $this->editorialContent = $editorialReview->Content;
            }
            if ($editorialReview->IsLinkSuppressed) {
                $this->editorialIsLinkSuppressed = $editorialReview->IsLinkSuppressed;
            }
        }
        if ($source->SimilarProducts && $source->SimilarProducts->SimilarProduct) {
            foreach ($source->SimilarProducts->SimilarProduct as $similarProduct) {
                $this->similarProducts[] = new SimilarProduct($similarProduct);
            }
        }
        if ($source->BrowseNodes && $source->BrowseNodes->BrowseNode) {
            foreach ($source->BrowseNodes->BrowseNode as $browseNode) {
                $this->browseNodes[] = new BrowseNode($browseNode);
            }
        }
    }

    /**
     * @return string
     */
    public function getAsin()
    {
        return $this->asin;
    }

    /**
     * @return string
     */
    public function getDetailPageUrl()
    {
        return $this->detailPageUrl;
    }

    /**
     * @return array
     */
    public function getItemLinks()
    {
        return $this->itemLinks;
    }

    /**
     * @return int
     */
    public function getSalesRank()
    {
        return $this->salesRank;
    }

    /**
     * @return Image
     */
    public function getLargeImage()
    {
        return $this->largeImage;
    }

    /**
     * @return ItemAttributes
     */
    public function getItemAttributes()
    {
        return $this->itemAttributes;
    }

    /**
     * @return mixed
     */
    public function getOfferSummary()
    {
        return $this->offerSummary;
    }

    /**
     * @return OfferCollection
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @return boolean
     */
    public function isHasCustomerReviews()
    {
        return $this->hasCustomerReviews;
    }

    /**
     * @return string
     */
    public function getEditorialSource()
    {
        return $this->editorialSource;
    }

    /**
     * @return string
     */
    public function getEditorialContent()
    {
        return $this->editorialContent;
    }

    /**
     * @return boolean
     */
    public function isEditorialIsLinkSuppressed()
    {
        return $this->editorialIsLinkSuppressed;
    }

    /**
     * @return SimilarProduct[]
     */
    public function getSimilarProducts()
    {
        return $this->similarProducts;
    }

    /**
     * @return BrowseNode[]
     */
    public function getBrowseNodes()
    {
        return $this->browseNodes;
    }
}