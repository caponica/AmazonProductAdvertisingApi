<?php

namespace CaponicaAmazonPaa\Response;

/**
 * Represents a SimilarProduct returned by a PAA response
 */
class SimilarProduct
{
    private $asin;
    private $title;

    public function __construct(\SimpleXMLElement $source) {
        if ($source->ASIN) {
            $this->asin = (string)$source->ASIN;
        }
        if ($source->Title) {
            $this->title = (string)$source->Title;
        }
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
    public function getTitle()
    {
        return $this->title;
    }
}