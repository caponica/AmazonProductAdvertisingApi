<?php

namespace CaponicaAmazonPaa\Response;

/**
 * Represents Dimensions returned by a PAA response
 */
class Dimensions
{
    const CONVERSION_FACTOR_KG_TO_LB    = 2.20462;

    const UNITS_LENGTH_HUNDREDTH_INCH       = 'hundredths-inches';
    const UNITS_LENGTH_HUNDREDTH_INCH_DE    = 'Hundertstel Zoll';
    const UNITS_WEIGHT_HUNDREDTH_POUND      = 'hundredths-pounds';
    const UNITS_WEIGHT_HUNDREDTH_POUND_DE   = 'Hundertstel Pfund';
    const UNITS_WEIGHT_KILOGRAMS            = 'Kilograms';

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

    public function __construct(\SimpleXMLElement $source) {
        $integerFields = [
            'Height' => 'height',
            'Length' => 'length',
            'Weight' => 'weight',
            'Width'  => 'width',
        ];
        foreach ($integerFields as $xmlName => $propertyName) {
            if ($source->$xmlName) {
                $this->$propertyName = (int)$source->$xmlName;
                if ($source->{$xmlName}['Units']) {
                    $this->units[$propertyName] = $source->{$xmlName}['Units'];
                }
            }
        }
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
        if (empty($this->weight)) {
            throw new \Exception("No weight given");
        } elseif (empty($this->units['weight'])) {
            throw new \Exception("Missing units for weight");
        } elseif ($this->isUnitsHundredthPound($this->units['weight'])) {
            return $this->weight / 100 / self::CONVERSION_FACTOR_KG_TO_LB;
        } elseif ($this->units['weight'] == self::UNITS_WEIGHT_KILOGRAMS) {
            return $this->weight;
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
        } elseif (empty($this->units['weight'])) {
            throw new \Exception("Missing units for weight");
        } elseif ($this->isUnitsHundredthPound($this->units['weight'])) {
            return $this->weight / 100;
        } elseif ($this->units['weight'] == self::UNITS_WEIGHT_KILOGRAMS) {
            return $this->weight * self::CONVERSION_FACTOR_KG_TO_LB;
        }
        throw new \Exception("Unknown units for weight : " . $this->units['weight']);
    }
    /**
     * @deprecated          The name of this method was confusing. Use getNormalisedDimensionsInHundredthsInches() instead
     *                      or use getNormalisedDimensionsInDecimalInches() if you want 'inch' values
     * @return array
     * @throws \Exception
     */
    public function getNormalisedDimensionsInInches() {
        return $this->getNormalisedDimensionsInHundredthsInches();
    }
    /**
     * @return array        array of three values (height, width, length) in decimal inches, ordered from smallest to largest
     *                      e.g. a value of [1.1,2,3] means 1.1x2x3 inches
     * @throws \Exception
     */
    public function getNormalisedDimensionsInDecimalInches() {
        $dimensions = $this->getNormalisedDimensionsInHundredthsInches();
        $dimensions = [
            $dimensions[0] / 100,
            $dimensions[1] / 100,
            $dimensions[2] / 100,
        ];
        return $dimensions;
    }
    /**
     * @return array        array of three values (height, width, length) in hundredths of inches, ordered from smallest to largest
     *                      e.g. a value of [100,200,300] means 1x2x3 inches
     * @throws \Exception
     */
    public function getNormalisedDimensionsInHundredthsInches() {
        $dimensions = [];
        $directions = ['height', 'width', 'length'];
        foreach ($directions as $direction) {
            if (empty($this->$direction)) {
                throw new \Exception("Missing dimension for $direction");
            } elseif (empty($this->units[$direction])) {
                throw new \Exception("Missing units for $direction");
            } elseif (!$this->isUnitsHundredthInch($this->units[$direction])) { // @todo - does Amazon ever give decimal lengths?
                throw new \Exception("Unknown units for $direction : " . $this->units[$direction]);
            } else {
                $dimensions[] = $this->$direction;
            }
        }
        sort($dimensions);
        return $dimensions;
    }

    public function isUnitsHundredthInch($unitString) {
        if ($unitString == self::UNITS_LENGTH_HUNDREDTH_INCH) {
            return true;
        }
        if ($unitString == self::UNITS_LENGTH_HUNDREDTH_INCH_DE) {
            return true;
        }
        return false;
    }
    public function isUnitsHundredthPound($unitString) {
        if ($unitString == self::UNITS_WEIGHT_HUNDREDTH_POUND) {
            return true;
        }
        if ($unitString == self::UNITS_WEIGHT_HUNDREDTH_POUND_DE) {
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