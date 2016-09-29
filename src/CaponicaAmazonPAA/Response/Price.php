<?php

namespace CaponicaAmazonPAA\Response;

/**
 * Represents a price returned by a PAA response
 */
class Price
{
    private $amount;
    private $currencyCode;
    private $formattedPrice;

    public function __construct(\SimpleXMLElement $source) {
        if ($source->Amount) {
            $this->amount = (int)$source->Amount;
        }
        if ($source->CurrencyCode) {
            $this->currencyCode = (string)$source->CurrencyCode;
        }
        if ($source->FormattedPrice) {
            $this->formattedPrice = (string)$source->FormattedPrice;
        }
    }

    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################
    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getFormattedPrice()
    {
        return $this->formattedPrice;
    }
}