<?php

namespace CaponicaAmazonPAA\Response;

/**
 * A generic set of parameters for a PAA request
 */
class ItemLookupResponse
{
    private $rawXml;
    private $parsedXml;
    private $operationRequest;
    private $item;

    public function __construct($rawXml)
    {
        $this->rawXml = $rawXml;
        $this->parsedXml = new \SimpleXMLElement($rawXml);
        if ($this->parsedXml->OperationRequest) {
            $this->operationRequest = new OperationRequest($this->parsedXml->OperationRequest);
        }
        if ($this->parsedXml->Items && $this->parsedXml->Items->Item) {
            $this->item = new Item($this->parsedXml->Items->Item);
        }
    }

    public function getItem() {
        return $this->item;
    }

    public function getEditorialReviewContent() {
//        print_r($this->parsedXml);
        return $this->item->getEditorialContent();
    }
}