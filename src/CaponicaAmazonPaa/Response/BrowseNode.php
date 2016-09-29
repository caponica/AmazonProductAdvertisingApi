<?php

namespace CaponicaAmazonPaa\Response;

/**
 * Represents a BrowseNode returned by a PAA response
 */
class BrowseNode
{
    private $browseNodeId;
    private $name;
    private $ancestor = null;

    public function __construct(\SimpleXMLElement $source) {
        if ($source->BrowseNodeId) {
            $this->browseNodeId = (string)$source->BrowseNodeId;
        }
        if ($source->Name) {
            $this->name = (string)$source->Name;
        }
        if ($source->Ancestors && $source->Ancestors->BrowseNode) {
            $this->ancestor = new BrowseNode($source->Ancestors->BrowseNode);
        }
    }

    public function getTopLevelBrowseNode() {
        if ($this->ancestor) {
            return $this->ancestor->getTopLevelBrowseNode();
        } else {
            return $this;
        }
    }

    // ##################################################
    // #  auto-generated basic getters live below here  #
    // ##################################################

    /**
     * @return string
     */
    public function getBrowseNodeId()
    {
        return $this->browseNodeId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}