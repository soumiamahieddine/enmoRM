<?php
namespace core\Type;
/**
 * Class for binary data
 */
class Binary
    extends abstractBinary
{
    
    /**
     * Return the base64 of data
     * @return string
     */
    public function base64encode()
    {
        return base64_encode($this->data);
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

        $return .= "base64," . base64_encode($this->data);

        return $return;
    }

}