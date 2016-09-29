<?php

namespace CaponicaAmazonPAA\Response;

/**
 * A generic set of parameters for a PAA request
 */
class OperationRequest
{
    private $requestId;
    private $arguments = [];
    private $requestProcessingTime;

    public function __construct(\SimpleXMLElement $source) {
        $this->requestId = (string)$source->RequestId;
        $this->requestProcessingTime = (float)$source->RequestProcessingTime;
        foreach ($source->Arguments->Argument as $argument) {
            $name = (string)$argument['Name'];
            $value = (string)$argument['Value'];
            $this->arguments[$name] = $value;
        }
    }

    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################
    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return float
     */
    public function getRequestProcessingTime()
    {
        return $this->requestProcessingTime;
    }
}