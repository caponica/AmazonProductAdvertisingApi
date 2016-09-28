<?php

namespace CaponicaAmazonPAA\Response;

/**
 * Represents an image returned by a PAA response
 */
class Image
{
    const IMAGE_URL_SUFFIX_FOR_DIFFERENT_WIDTH = '._SL%width%_';
    private $url;
    private $height;
    private $width;

    public function __construct(\SimpleXMLElement $source) {
        $this->url      = (string)$source->URL;
        $this->height   = (int)$source->Height;
        $this->width    = (int)$source->Width;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    public function getUrlForWidth($width) {
        if (!$this->url) {
            return null;
        }
        $baseImageUrl = substr($this->url, 0, -4);
        $imageExtension = substr($this->url, -4);
        $widthSuffix = str_replace('%width%', $width, self::IMAGE_URL_SUFFIX_FOR_DIFFERENT_WIDTH);
        return $baseImageUrl . $widthSuffix . $imageExtension;
    }

}