<?php
namespace core\Type;
/**
 * Class for Xml documents
 */
class Xml
    extends \DOMDocument
    implements \JsonSerializable
{
    
    /**
     * Construct a new xml object
     * @param string $xml
     * @param string $encoding
     */
    public function __construct($xml=false, $encoding=false)
    {
        if (!$encoding) {
            $encoding = \laabs::getXmlEncoding();
        }

        parent::__construct("1.0", $encoding);

        if ($xml) {
            $this->loadXml($xml);
        }

    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return $this->saveXml();
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return base64_encode($this->__toString());
    }

}