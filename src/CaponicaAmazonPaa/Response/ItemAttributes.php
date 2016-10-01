<?php

namespace CaponicaAmazonPaa\Response;

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
    private $size;
    private $studio;
    private $title;
    private $upcList = [];
    private $upc;
    private $warranty;

    public function __construct(\SimpleXMLElement $source) {
        $stringFields = [
            'Binding'         => 'binding',
            'Brand'           => 'brand',
            'EAN'             => 'ean',
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
            'Size'            => 'size',
            'Studio'          => 'studio',
            'Title'           => 'title',
            'UPC'             => 'upc',
            'Warranty'        => 'warranty',
        ];
        foreach ($stringFields as $xmlName => $propertyName) {
            if ($source->$xmlName) {
                $this->$propertyName = (string)$source->$xmlName;
            }
        }

        $integerFields = [
            'ManufacturerMaximumAge' => 'manufacturerMaximumAge', // @todo - store Units (from attribute)
            'ManufacturerMinimumAge' => 'manufacturerMinimumAge', // @todo - store Units (from attribute)
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
                $this->features[] = (string)$feature;
            }
        }
        if ($source->Languages && $source->Languages->Language) {
            foreach ($source->Languages->Language as $language) {
                $this->languages[] = (string)$language->Name; // @todo - do we want to store language->Type as well?
            }
            $this->languages = array_unique($this->languages);
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

    public function getLanguageListAsSortedString() {
        $languages = [];
        foreach ($this->languages as $language) {
            $languages[] = $language;
        }
        sort($languages);
        $languageList = implode(',', $languages);
        return $languageList;
    }
    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################

    /**
     * @return mixed
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return array
     */
    public function getCatalogNumberList()
    {
        return $this->catalogNumberList;
    }

    /**
     * @return array
     */
    public function getEanList()
    {
        return $this->eanList;
    }

    /**
     * @return mixed
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return mixed
     */
    public function getIsAdultProduct()
    {
        return $this->isAdultProduct;
    }

    /**
     * @return Dimensions
     */
    public function getItemDimensions()
    {
        return $this->itemDimensions;
    }

    /**
     * @return mixed
     */
    public function getItemPartNumber()
    {
        return $this->itemPartNumber;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return Price
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * @return mixed
     */
    public function getMpn()
    {
        return $this->mpn;
    }

    /**
     * @return mixed
     */
    public function getManufacturerMaximumAge()
    {
        return $this->manufacturerMaximumAge;
    }

    /**
     * @return mixed
     */
    public function getManufacturerMinimumAge()
    {
        return $this->manufacturerMinimumAge;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Dimensions
     */
    public function getPackageDimensions()
    {
        return $this->packageDimensions;
    }

    /**
     * @return mixed
     */
    public function getPackageQuantity()
    {
        return $this->packageQuantity;
    }

    /**
     * @return mixed
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }

    /**
     * @return mixed
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * @return mixed
     */
    public function getProductTypeName()
    {
        return $this->productTypeName;
    }

    /**
     * @return mixed
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getStudio()
    {
        return $this->studio;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getUpcList()
    {
        return $this->upcList;
    }

    /**
     * @return mixed
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * @return mixed
     */
    public function getWarranty()
    {
        return $this->warranty;
    }
}
