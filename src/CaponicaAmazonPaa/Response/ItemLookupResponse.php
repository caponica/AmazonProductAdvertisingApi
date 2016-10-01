<?php

namespace CaponicaAmazonPaa\Response;

/**
 * A generic set of parameters for a PAA request
 */
class ItemLookupResponse
{
    private $rawXml;
    private $parsedXml;
    private $operationRequest;
    /** @var  Item[] */
    private $items = [];

    public function __construct($rawXml)
    {
        $this->rawXml = $rawXml;
        $this->parsedXml = new \SimpleXMLElement($rawXml);
        if ($this->parsedXml->OperationRequest) {
            $this->operationRequest = new OperationRequest($this->parsedXml->OperationRequest);
        }
        if ($this->parsedXml->Items && $this->parsedXml->Items->Item) {
            foreach ($this->parsedXml->Items->Item as $item) {
                $asin = (string)$item->ASIN;
                $this->items[$asin] = new Item($item);
            }
        }
    }

    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################

    /**
     * @return string
     */
    public function getRawXml()
    {
        return $this->rawXml;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getParsedXml()
    {
        return $this->parsedXml;
    }

    /**
     * @return OperationRequest
     */
    public function getOperationRequest()
    {
        return $this->operationRequest;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }


}