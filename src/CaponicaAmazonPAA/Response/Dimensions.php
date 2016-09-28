<?php

namespace CaponicaAmazonPAA\Response;

/**
 * Represents Dimensions returned by a PAA response
 */
class Dimensions
{
    private $height;
    private $length;
    private $weight;
    private $width;

    public function __construct(\SimpleXMLElement $source) {
        $integerFields = [
            'height' => 'Height',
            'length' => 'Length',
            'weight' => 'Weight',
            'width'  => 'Width',
        ];
        foreach ($integerFields as $xmlName => $propertyName) {
            if ($source->$xmlName) {
                $this->$propertyName = (int)$source->$xmlName;
            }
        }
    }

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