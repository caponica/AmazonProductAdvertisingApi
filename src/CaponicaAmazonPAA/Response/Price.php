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
}