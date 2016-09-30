<?php

namespace CaponicaAmazonPaa\Response;

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
    private $parentAsin;
    /** @var string */
    private $detailPageUrl;
    /** @var array */
    private $itemLinks = [];
    /** @var int */
    private $salesRank;
    /** @var Image */
    private $mainImage;
    /** @var ItemAttributes */
    private $itemAttributes;
    private $secondaryImages = []; // from ImageSets. Does not include main image (which is in mainImage)
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
        if ($source->ParentASIN) {
            $this->parentAsin = (string)$source->ParentASIN;
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
            $this->mainImage = new Image($source->LargeImage);
        }
        if ($source->ImageSets && $source->ImageSets->ImageSet) {
            foreach ($source->ImageSets->ImageSet as $imageSet) {
                if ($imageSet->LargeImage && $imageSet->LargeImage->URL) {
                    if ($this->mainImage && $this->mainImage->getUrl() == $imageSet->LargeImage->URL) {
                        continue;
                    }
                    $this->secondaryImages[] = new Image($imageSet->LargeImage);
                }
            }
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
                $this->editorialSource = (string)$editorialReview->Source;
            }
            if ($editorialReview->Content) {
                $this->editorialContent = (string)$editorialReview->Content;
            }
            if ($editorialReview->IsLinkSuppressed) {
                $this->editorialIsLinkSuppressed = (string)$editorialReview->IsLinkSuppressed;
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
     * Alias method for getMainImage()
     *
     * @return Image
     */
    public function getLargeImage()
    {
        return $this->getMainImage();
    }
    public function getBuyBoxOfferPriceAmount() {
        if ($this->getOffers() && ($offerPrice = $this->getOffers()->getOfferPrice())) {
            return $offerPrice->getAmount();
        }
        return null;
    }
    public function getBuyBoxOfferPriceCurrency() {
        if ($this->getOffers() && ($offerPrice = $this->getOffers()->getOfferPrice())) {
            return $offerPrice->getCurrencyCode();
        }
        return null;
    }

    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################

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
    public function getParentAsin()
    {
        return $this->parentAsin;
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
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @return ItemAttributes
     */
    public function getItemAttributes()
    {
        return $this->itemAttributes;
    }

    /**
     * @return array
     */
    public function getSecondaryImages()
    {
        return $this->secondaryImages;
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