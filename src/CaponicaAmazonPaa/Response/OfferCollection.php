<?php

namespace CaponicaAmazonPaa\Response;

/**
 * Represents an Offers collection returned by a PAA response
 */
class OfferCollection
{
    private $totalOffers;
    private $totalOfferPages;
    private $moreOffersUrl;
    private $offerMerchantName;
    private $offerCondition;
    private $offerListingId;
    private $offerPrice;
    private $offerAmountSaved;
    private $offerAvailability;
    private $offerAvailabilityType;
    private $offerAvailabilityMinHours;
    private $offerAvailabilityMaxHours;
    private $offerPercentageSaved;
    private $isEligibleForSuperSaverShipping;
    private $isEligibleForPrime;

    public function __construct(\SimpleXMLElement $source) {
        if ($source->TotalOffers) {
            $this->totalOffers = (int)$source->TotalOffers;
        }
        if ($source->TotalOfferPages) {
            $this->totalOfferPages = (int)$source->TotalOfferPages;
        }
        if ($source->MoreOffersUrl) {
            $this->moreOffersUrl = (string)$source->MoreOffersUrl;
        }

        // only handling one Offer here. I don't know when the PAA would return more than one.
        if ($source->Offer) {
            if ($source->Offer->Merchant && $source->Offer->Merchant->Name) {
                $this->offerMerchantName = (string)$source->Offer->Merchant->Name;
            }
            if ($source->Offer->OfferAttributes && $source->Offer->OfferAttributes->Condition) {
                $this->offerCondition = (string)$source->Offer->OfferAttributes->Condition;
            }
            if ($source->Offer->OfferListing) {
                $offerListing = $source->Offer->OfferListing;
                if ($offerListing->OfferListingId) {
                    $this->offerListingId = (string)$offerListing->OfferListingId;
                }
                if ($offerListing->Price) {
                    $this->offerPrice = new Price($offerListing->Price);
                }
                if ($offerListing->AmountSaved) {
                    $this->offerAmountSaved = new Price($offerListing->AmountSaved);
                }
                if ($offerListing->PercentageSaved) {
                    $this->offerPercentageSaved = (int)$offerListing->PercentageSaved;
                }
                if ($offerListing->Availability) {
                    $this->offerAvailability = (string)$offerListing->Availability;
                }
                if ($offerListing->AvailabilityAttributes) {
                    if ($offerListing->AvailabilityAttributes->AvailabilityType) {
                        $this->offerAvailabilityType = (string)$offerListing->AvailabilityAttributes->AvailabilityType;
                    }
                    if ($offerListing->AvailabilityAttributes->MinimumHours) {
                        $this->offerAvailabilityMinHours = (string)$offerListing->AvailabilityAttributes->MinimumHours;
                    }
                    if ($offerListing->AvailabilityAttributes->MaximumHours) {
                        $this->offerAvailabilityMaxHours = (string)$offerListing->AvailabilityAttributes->MaximumHours;
                    }
                    if ($offerListing->IsEligibleForSuperSaverShipping) {
                        $this->isEligibleForSuperSaverShipping = (boolean)$offerListing->IsEligibleForSuperSaverShipping;
                    }
                    if ($offerListing->IsEligibleForPrime) {
                        $this->isEligibleForPrime = (boolean)$offerListing->IsEligibleForPrime;
                    }
                }
            }
        }

    }

    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################

    /**
     * @return int
     */
    public function getTotalOffers()
    {
        return $this->totalOffers;
    }

    /**
     * @return int
     */
    public function getTotalOfferPages()
    {
        return $this->totalOfferPages;
    }

    /**
     * @return string
     */
    public function getMoreOffersUrl()
    {
        return $this->moreOffersUrl;
    }

    /**
     * @return string
     */
    public function getOfferMerchantName()
    {
        return $this->offerMerchantName;
    }

    /**
     * @return string
     */
    public function getOfferCondition()
    {
        return $this->offerCondition;
    }

    /**
     * @return string
     */
    public function getOfferListingId()
    {
        return $this->offerListingId;
    }

    /**
     * @return Price
     */
    public function getOfferPrice()
    {
        return $this->offerPrice;
    }

    /**
     * @return Price
     */
    public function getOfferAmountSaved()
    {
        return $this->offerAmountSaved;
    }

    /**
     * @return string
     */
    public function getOfferAvailability()
    {
        return $this->offerAvailability;
    }

    /**
     * @return string
     */
    public function getOfferAvailabilityType()
    {
        return $this->offerAvailabilityType;
    }

    /**
     * @return string
     */
    public function getOfferAvailabilityMinHours()
    {
        return $this->offerAvailabilityMinHours;
    }

    /**
     * @return string
     */
    public function getOfferAvailabilityMaxHours()
    {
        return $this->offerAvailabilityMaxHours;
    }

    /**
     * @return int
     */
    public function getOfferPercentageSaved()
    {
        return $this->offerPercentageSaved;
    }

    /**
     * @return boolean
     */
    public function isIsEligibleForSuperSaverShipping()
    {
        return $this->isEligibleForSuperSaverShipping;
    }

    /**
     * @return boolean
     */
    public function isIsEligibleForPrime()
    {
        return $this->isEligibleForPrime;
    }
}