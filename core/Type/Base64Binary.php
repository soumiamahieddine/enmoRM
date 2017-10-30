<?php
namespace core\Type;
/**
 * Class for binary data encoded in base64
 */
class Base64Binary
    extends abstractBinary
{

    /**
     * Return the base64 of data
     * @return string
     */
    public function base64decode()
    {
        return base64_decode($this->data);
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return base64_encode($this->data);
    }

    /**
     * Return the url base64 of data
     * @return string
     */
    public function urlEncode()
    {
        $return = "data:" . $this->encoding . ";";
        
        if ($this->charset) {
            $return .= "charset:" . $this->charset . ";";
        }

        $return .= "base64," . $this->data;

        return $return;
    }

}