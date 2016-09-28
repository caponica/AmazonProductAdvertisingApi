<?php

namespace CaponicaAmazonPAA\Response;

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
    private $offerAvailability;
    private $offerAvailabilityType;
    private $offerAvailabilityMinHours;
    private $offerAvailabilityMaxHours;
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

/*

 */

}