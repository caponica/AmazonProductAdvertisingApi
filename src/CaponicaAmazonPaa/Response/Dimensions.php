<?php

namespace CaponicaAmazonPaa\Response;

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ItemInfo;

/**
 * Represents Dimensions returned by a PAA response
 */
class Dimensions
{
    const CONVERSION_FACTOR_KG_TO_LB    = 2.20462;

    const UNITS_WEIGHT_GRAMS                = 'grams';
    const UNITS_WEIGHT_KILOGRAMS            = 'kilograms';
    const UNITS_WEIGHT_POUND                = 'pounds';
    const UNITS_WEIGHT_POUND_DE             = 'pfund';
    const UNITS_WEIGHT_POUND_ES             = 'libras'; // not checked
    const UNITS_WEIGHT_POUND_FR             = 'livres';
    const UNITS_WEIGHT_POUND_IT             = 'sterline'; // not checked
    const UNITS_WEIGHT_POUND_NL             = 'pond'; // not checked

    const UNITS_LENGTH_INCH                 = 'inches';
    const UNITS_LENGTH_INCH_DE              = 'zoll';
    const UNITS_LENGTH_INCH_ES              = 'pulgadas'; // not checked
    const UNITS_LENGTH_INCH_FR              = 'pouces';
    const UNITS_LENGTH_INCH_IT              = 'pollice'; // not checked
    const UNITS_LENGTH_INCH_NL              = 'inch'; // not checked

    private $height;
    private $length;
    private $weight;
    private $width;
    private $units = [
        'height' => null,
        'length' => null,
        'width'  => null,
        'weight' => null,
    ];

    public function __construct(ItemInfo $itemInfo) {
        if (!$itemInfo->getProductInfo()) {
            throw new \InvalidArgumentException('No productInfo provided in Dimensions constructor');
        }
        if (!$itemDimensions = $itemInfo->getProductInfo()->getItemDimensions()) {
            throw new \InvalidArgumentException('No itemDimensions provided in Dimensions constructor');
        }

        $weight = $itemInfo->getProductInfo()->getItemDimensions()->getWeight();
        $height = $itemInfo->getProductInfo()->getItemDimensions()->getHeight();
        $length = $itemInfo->getProductInfo()->getItemDimensions()->getLength();
        $width  = $itemInfo->getProductInfo()->getItemDimensions()->getWidth();

        if ($weight) {
            $this->weight = $weight->getDisplayValue();
            $this->units['weight'] = $weight->getUnit();
        }
        if ($height) {
            $this->height = $height->getDisplayValue();
            $this->units['height'] = $height->getUnit();
        }
        if ($length) {
            $this->length = $length->getDisplayValue();
            $this->units['length'] = $length->getUnit();
        }
        if ($width) {
            $this->width = $width->getDisplayValue();
            $this->units['width'] = $width->getUnit();
        }
    }

    public function hasWeight() {
        return !empty($this->weight) && !empty($this->units['weight']);
    }
    /**
     * @return int
     * @throws \Exception
     */
    public function getWeightInGrams() {
        $weightInGrams = ceil($this->getWeightInKg() * 1000);
        return $weightInGrams;
    }
    /**
     * @return float
     * @throws \Exception
     */
    public function getWeightInKg() {
        if (!$this->hasWeight()) {
            throw new \Exception("No weight given");
        } elseif ($this->isUnitsPounds($this->units['weight'])) {
            return $this->weight / self::CONVERSION_FACTOR_KG_TO_LB;
        } elseif ($this->isUnitsKilograms($this->units['weight'])) {
            return $this->weight;
        } elseif ($this->isUnitsGrams($this->units['weight'])) {
            return $this->weight / 1000;
        }
        throw new \Exception("Unknown units for weight : " . $this->units['weight']);
    }
    /**
     * @return float
     * @throws \Exception
     */
    public function getWeightInPounds() {
        if (empty($this->weight)) {
            throw new \Exception("No weight given");
        } elseif ($this->isUnitsPounds($this->units['weight'])) {
            return $this->weight;
        } elseif ($this->isUnitsKilograms($this->units['weight'])) {
            return $this->weight * self::CONVERSION_FACTOR_KG_TO_LB;
        } elseif ($this->isUnitsGrams($this->units['weight'])) {
            return $this->getWeightInKg() * self::CONVERSION_FACTOR_KG_TO_LB;
        }
        throw new \Exception("Unknown units for weight : " . $this->units['weight']);
    }

    /**
     * @return array        array of three values (height, width, length) in decimal inches, ordered from smallest to largest
     *                      e.g. a value of [1.1,2,3] means 1.1x2x3 inches
     * @throws \Exception
     */
    public function getNormalisedDimensionsInDecimalInches() {
        $dimensions = [];
        $directions = ['height', 'width', 'length'];
        foreach ($directions as $direction) {
            if (empty($this->$direction)) {
                throw new \Exception("Missing dimension for $direction");
            } elseif (empty($this->units[$direction])) {
                throw new \Exception("Missing units for $direction");
            } elseif (!$this->isUnitsInches($this->units[$direction])) {
                throw new \Exception("Unknown units for $direction : " . $this->units[$direction]);
            } else {
                $dimensions[] = $this->$direction;
            }
        }
        sort($dimensions);
        return $dimensions;
    }
    /**
     * @return array        array of three values (height, width, length) in hundredths of inches, ordered from smallest to largest
     *                      e.g. a value of [100,200,300] means 1x2x3 inches
     * @throws \Exception
     */
    public function getNormalisedDimensionsInHundredthsInches() {
        $dimensions = $this->getNormalisedDimensionsInDecimalInches();
        $dimensions = [
            $dimensions[0] * 100,
            $dimensions[1] * 100,
            $dimensions[2] * 100,
        ];
        return $dimensions;
    }

    public function isUnitsInches($unitString) {
        $unitString = strtolower($unitString);
        if ($unitString == self::UNITS_LENGTH_INCH) {
            return true;
        }
        if ($unitString == self::UNITS_LENGTH_INCH_DE) {
            return true;
        }
        if ($unitString == self::UNITS_LENGTH_INCH_ES) {
            return true;
        }
        if ($unitString == self::UNITS_LENGTH_INCH_FR) {
            return true;
        }
        if ($unitString == self::UNITS_LENGTH_INCH_IT) {
            return true;
        }
        if ($unitString == self::UNITS_LENGTH_INCH_NL) {
            return true;
        }
        return false;
    }
    private function isUnitsKilograms($unitString) {
        $unitString = strtolower($unitString);
        if ($unitString == self::UNITS_WEIGHT_KILOGRAMS) {
            return true;
        }
        return false;
    }
    private function isUnitsGrams($unitString) {
        $unitString = strtolower($unitString);
        if ($unitString == self::UNITS_WEIGHT_GRAMS) {
            return true;
        }
        return false;
    }
    private function isUnitsPounds($unitString) {
        $unitString = strtolower($unitString);
        if ($unitString == self::UNITS_WEIGHT_POUND) {
            return true;
        }
        if ($unitString == self::UNITS_WEIGHT_POUND_DE) {
            return true;
        }
        if ($unitString == self::UNITS_WEIGHT_POUND_ES) {
            return true;
        }
        if ($unitString == self::UNITS_WEIGHT_POUND_FR) {
            return true;
        }
        if ($unitString == self::UNITS_WEIGHT_POUND_IT) {
            return true;
        }
        if ($unitString == self::UNITS_WEIGHT_POUND_NL) {
            return true;
        }
        return false;
    }
    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################
    public function getHeight()
    {
        return $this->height;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getWidth()
    {
        return $this->width;
    }
}