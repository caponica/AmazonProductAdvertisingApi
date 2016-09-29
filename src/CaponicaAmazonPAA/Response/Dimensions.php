<?php

namespace CaponicaAmazonPAA\Response;

/**
 * Represents Dimensions returned by a PAA response
 */
class Dimensions
{
    const CONVERSION_FACTOR_KG_TO_LB    = 2.20462;

    const UNITS_LENGTH_HUNDREDTH_INCH   = 'hundredths-inches';
    const UNITS_WEIGHT_HUNDREDTH_POUND  = 'hundredths-pounds';
    const UNITS_WEIGHT_KILOGRAMS        = 'Kilograms';

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
     * @return float
     * @throws \Exception
     */
    public function getNormalisedWeightInKg() {
        if (empty($this->weight)) {
            throw new \Exception("No weight given");
        } elseif (empty($this->units['weight'])) {
            throw new \Exception("Missing units for weight");
        } elseif ($this->units['weight'] == self::UNITS_WEIGHT_HUNDREDTH_POUND) {
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
    public function getNormalisedWeightInPounds() {
        if (empty($this->weight)) {
            throw new \Exception("No weight given");
        } elseif (empty($this->units['weight'])) {
            throw new \Exception("Missing units for weight");
        } elseif ($this->units['weight'] == self::UNITS_WEIGHT_HUNDREDTH_POUND) {
            return $this->weight / 100;
        } elseif ($this->units['weight'] == self::UNITS_WEIGHT_KILOGRAMS) {
            return $this->weight * self::CONVERSION_FACTOR_KG_TO_LB;
        }
        throw new \Exception("Unknown units for weight : " . $this->units['weight']);
    }
    /**
     * @return array        array of three values (height, width, length) in inches, ordered from smallest to largest
     * @throws \Exception
     */
    public function getNormalisedDimensionsInInches() {
        $dimensions = [];
        $directions = ['height', 'width', 'length'];
        foreach ($directions as $direction) {
            if (empty($this->$direction)) {
                throw new \Exception("Missing dimension for $direction");
            } elseif (empty($this->units[$direction])) {
                throw new \Exception("Missing units for $direction");
            } elseif ($this->units[$direction] != self::UNITS_LENGTH_HUNDREDTH_INCH) { // @todo - does Amazon ever give decimal lengths?
                throw new \Exception("Unknown units for $direction : " . $this->units[$direction]);
            } else {
                $dimensions[] = $this->$direction;
            }
        }
        sort($dimensions);
        return $dimensions;
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