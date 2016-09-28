<?php

namespace CaponicaAmazonPAA\Response;

/**
 * Represents ItemAttributes returned by a PAA response
 */
class ItemAttributes
{
    private $binding;
    private $brand;
    private $catalogNumberList = [];
    private $eanList = [];
    private $ean;
    private $features = [];
    private $isAdultProduct;
    private $itemDimensions;
    private $itemPartNumber;
    private $label;
    private $languages = [];
    private $listPrice;
    private $mpn;
    private $manufacturerMaximumAge;
    private $manufacturerMinimumAge;
    private $manufacturer;
    private $model;
    private $packageDimensions;
    private $packageQuantity;
    private $partNumber;
    private $productGroup;
    private $productTypeName;
    private $publisher;
    private $studio;
    private $title;
    private $upcList = [];
    private $upc;

    public function __construct(\SimpleXMLElement $source) {
        $stringFields = [
            'Binding'         => 'binding',
            'Brand'           => 'brand',
            'EAN'             => 'ean',
            'Feature'         => 'feature',
            'IsAdultProduct'  => 'isAdultProduct',
            'ItemPartNumber'  => 'itemPartNumber',
            'Label'           => 'label',
            'MPN'             => 'mpm',
            'Manufacturer'    => 'manufacturer',
            'Model'           => 'model',
            'PartNumber'      => 'partNumber',
            'ProductGroup'    => 'productGroup',
            'ProductTypeName' => 'productTypeName',
            'Publisher'       => 'publisher',
            'Studio'          => 'studio',
            'Title'           => 'title',
            'UPC'             => 'upc',
        ];
        foreach ($stringFields as $xmlName => $propertyName) {
            if ($source->$xmlName) {
                $this->$propertyName = (string)$source->$xmlName;
            }
        }

        $integerFields = [
            'ManufacturerMaximumAge' => 'manufacturerMaximumAge',
            'ManufacturerMinimumAge' => 'manufacturerMinimumAge',
            'PackageQuantity'        => 'packageQuantity',
        ];
        foreach ($integerFields as $xmlName => $propertyName) {
            if ($source->$xmlName) {
                $this->$propertyName = (int)$source->$xmlName;
            }
        }

        $stringListFields = [
            'CatalogNumberList' => 'catalogNumberList',
            'EANList'           => 'eanList',
            'UPCList'           => 'upcList',
        ];
        foreach ($stringListFields as $xmlName => $propertyName) {
            $elementName = $xmlName . 'Element';
            if ($source->$xmlName && $source->$xmlName->$elementName) {
                foreach ($source->$xmlName->$elementName as $element) {
                    $this->{$propertyName}[] = (string)$element;
                }
            }
        }

        if ($source->Feature) {
            foreach ($source->Feature as $feature) {
                $this->features[] = $feature;
            }
        }
        if ($source->Languages && $source->Languages->Language) {
            foreach ($source->Languages->Language as $language) {
                $this->languages[] = $language->Name; // What is Language->Type?
            }
        }
        if ($source->ItemDimensions) {
            $this->itemDimensions = new Dimensions($source->ItemDimensions);
        }
        if ($source->PackageDimensions) {
            $this->packageDimensions = new Dimensions($source->PackageDimensions);
        }
        if ($source->ListPrice) {
            $this->listPrice = new Price($source->ListPrice);
        }
    }
}