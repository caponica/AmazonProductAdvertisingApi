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
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }


}