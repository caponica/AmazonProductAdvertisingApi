<?php

namespace CaponicaAmazonPaa\Response;

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\BrowseNode as BrowseNode;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ImageSize;

/**
 * Represents an item returned by a PAA response
 */
class Item
{
    /** @var \Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\Item */
    private $paaItem;

    /** @var Dimensions */
    private $dimensions;

    public function __construct(\Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\Item $paaItem) {
        $this->paaItem = $paaItem;
        try {
            $this->dimensions = new Dimensions($paaItem->getItemInfo());
        } catch (\InvalidArgumentException $e) {
            // No dimensions returned
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
        if ($this->paaItem->getOffers() && $this->paaItem->getOffers()->getListings()) {
            foreach ($this->paaItem->getOffers()->getListings() as $listing) {
                if ($listing->getIsBuyBoxWinner()) {
                    return $listing->getPrice()->getAmount();
                }
            }
        }
        return null;
    }
    public function getBuyBoxOfferPriceCurrency() {
        if ($this->paaItem->getOffers() && $this->paaItem->getOffers()->getListings()) {
            foreach ($this->paaItem->getOffers()->getListings() as $listing) {
                if ($listing->getIsBuyBoxWinner()) {
                    return $listing->getPrice()->getCurrency();
                }
            }
        }
        return null;
    }

    public function getFirstBarcode() {
        if ($this->paaItem->getItemInfo() && ($x = $this->paaItem->getItemInfo()->getExternalIds())) {
            if ($x->getEANs() && $x->getEANs()->getDisplayValues()) {
                foreach ($x->getEANs()->getDisplayValues() as $barcode) {
                    return self::canonicalBarcode($barcode);
                }
            }
            if ($x->getUPCs() && $x->getUPCs()->getDisplayValues()) {
                foreach ($x->getUPCs()->getDisplayValues() as $barcode) {
                    return self::canonicalBarcode($barcode);
                }
            }
        }
        return null;
    }
    public function getAllBarcodes() {
        $barcodes = [];
        if ($this->paaItem->getItemInfo() && ($x = $this->paaItem->getItemInfo()->getExternalIds())) {
            if ($x->getEANs() && $x->getEANs()->getDisplayValues()) {
                foreach ($x->getEANs()->getDisplayValues() as $barcode) {
                    $barcodes[] = self::canonicalBarcode($barcode);
                }
            }
            if ($x->getUPCs() && $x->getUPCs()->getDisplayValues()) {
                foreach ($x->getUPCs()->getDisplayValues() as $barcode) {
                    $barcodes[] = self::canonicalBarcode($barcode);
                }
            }
        }
        $barcodes = array_unique($barcodes);
        return $barcodes;
    }

    /**
     * Left pads a barcode with zeros up to 13 characters (EAN-13 standard)
     *
     * @param $barcode
     * @return string
     */
    private static function canonicalBarcode($barcode, $maxLength = 13) {
        $barcode = ltrim(trim($barcode), '0');
        return str_pad($barcode, $maxLength, '0', STR_PAD_LEFT);
    }

    public function getWeightInPounds() {
        if (!$this->dimensions || !$this->dimensions->hasWeight()) {
            return null;
        }
        return $this->dimensions->getWeightInPounds();
    }
    public function getWeightInGrams() {
        if (!$this->dimensions || !$this->dimensions->hasWeight()) {
            return null;
        }
        return $this->dimensions->getWeightInGrams();
    }
    public function getNormalisedDimensionsInDecimalInches() {
        if (!$this->dimensions) {
            return null;
        }
        return $this->dimensions->getNormalisedDimensionsInDecimalInches();
    }
    public function getNormalisedDimensionsInHundredthsInches() {
        if (!$this->dimensions) {
            return null;
        }
        return $this->dimensions->getNormalisedDimensionsInHundredthsInches();
    }
    public function hasAnyDimensions() {
        return !empty($this->dimensions);
    }

    /**
     * @return string
     */
    public function getAsin()
    {
        return $this->paaItem->getASIN();
    }

    /**
     * @return string
     */
    public function getParentAsin()
    {
        return $this->paaItem->getParentASIN();
    }

    /**
     * @return string
     */
    public function getDetailPageUrl()
    {
        return $this->paaItem->getDetailPageURL();
    }

    /**
     * @return int
     */
    public function getSalesRank()
    {
        if (!$x = $this->paaItem->getBrowseNodeInfo()) {
            return null;
        }
        if (!$x = $x->getWebsiteSalesRank()) {
            return null;
        }
        return $x->getSalesRank();
    }
    /**
     * @return string
     */
    public function getTopLevelCategoryDisplayName()
    {
        if (!$x = $this->paaItem->getBrowseNodeInfo()) {
            return null;
        }
        if (!$x = $x->getWebsiteSalesRank()) {
            return null;
        }
        return $x->getDisplayName();
    }

    /**
     * @return ImageSize
     */
    public function getMainImage()
    {
        if (!$x = $this->paaItem->getImages()) {
            return null;
        }
        if (!$x = $x->getPrimary()) {
            return null;
        }
        if (!$x = $x->getLarge()) {
            return null;
        }
        return $this->paaItem->getImages()->getPrimary()->getLarge();
    }

    /**
     * @return ImageSize[]
     */
    public function getSecondaryImages()
    {
        if (!$x = $this->paaItem->getImages()) {
            return null;
        }
        if (!$x = $x->getVariants()) {
            return null;
        }
        $imageUrls = [];
        foreach ($this->paaItem->getImages()->getVariants() as $image) {
            if ($image->getLarge()) {
                $imageUrls[] = $image->getLarge();
            }
        }
        return $imageUrls;
    }

    /**
     * @return BrowseNode[]
     */
    public function getBrowseNodes()
    {
        if (!$x = $this->paaItem->getBrowseNodeInfo()) {
            return null;
        }
        if (!$x = $x->getBrowseNodes()) {
            return null;
        }
        return $this->paaItem->getBrowseNodeInfo()->getBrowseNodes();
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getByLineInfo()) {
            return null;
        }
        if (!$x = $x->getBrand()) {
            return null;
        }
        return $this->paaItem->getItemInfo()->getByLineInfo()->getBrand()->getDisplayValue();
    }
    /**
     * @return string
     */
    public function getManufacturer() {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getByLineInfo()) {
            return null;
        }
        if (!$x = $x->getManufacturer()) {
            return null;
        }
        return $this->paaItem->getItemInfo()->getByLineInfo()->getManufacturer()->getDisplayValue();
    }
    /**
     * @return string
     */
    public function getModelName() {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getManufactureInfo()) {
            return null;
        }
        if (!$x = $x->getModel()) {
            return null;
        }
        return $this->paaItem->getItemInfo()->getManufactureInfo()->getModel()->getDisplayValue();
    }
    /**
     * @return string
     */
    public function getItemPartNumber() {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getManufactureInfo()) {
            return null;
        }
        if (!$x = $x->getItemPartNumber()) {
            return null;
        }
        return $this->paaItem->getItemInfo()->getManufactureInfo()->getItemPartNumber()->getDisplayValue();
    }
    public function getTitle() {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getTitle()) {
            return null;
        }
        return $this->paaItem->getItemInfo()->getTitle()->getDisplayValue();
    }

    /**
     * @return string[]|null
     */
    public function getItemFeatures() {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getFeatures()) {
            return null;
        }
        return $this->paaItem->getItemInfo()->getFeatures()->getDisplayValues();
    }

    public function getOfferMerchantName() {
        if (!$x = $this->paaItem->getOffers()) {
            return null;
        }
        if (!$x = $x->getListings()) {
            return null;
        }
        if (!$x = $x[0]->getMerchantInfo()) {
            return null;
        }
        return $this->paaItem->getOffers()->getListings()[0]->getMerchantInfo()->getName();
    }

    /**
     * Returns an array of languages for this Item
     *
     * @return array|null
     */
    public function getLanguages() {
        if (!$x = $this->paaItem->getItemInfo()) {
            return null;
        }
        if (!$x = $x->getContentInfo()) {
            return null;
        }
        if (!$x = $x->getLanguages()) {
            return null;
        }
        if (!$x = $x->getDisplayValues()) {
            return null;
        }
        $languages = [];
        foreach ($this->paaItem->getItemInfo()->getContentInfo()->getLanguages()->getDisplayValues() as $language) {
            $languages[] = $language->getDisplayValue();
        }
        sort($languages);
        return $languages;
    }
    public function getLanguagesAsSortedString() {
        $languagesArray = $this->getLanguages();
        if (empty($languagesArray)) {
            return null;
        }
        return implode(',', $languagesArray);
    }
}