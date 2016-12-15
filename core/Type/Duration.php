<?php
namespace core\Type;
/**
 * Class for duration type
 */
class Duration
    extends \DateInterval
    implements \JsonSerializable
{
    
    /**
     * Construct a new duration object
     * @param string $duration
     */
    public function __construct($duration)
    {
        if ($duration[0] == '-') {
            $invert = 1;
            $duration = substr($duration, 1);
        } else {
            $invert = false;
        }

        parent::__construct($duration);

        if ($invert) {
            $this->invert = 1;
        }
    }
    /**
     * Magic method to retrieve value
     * @return string
     */
    public function __toString()
    {
        $str = "";

        if ($this->invert) {
            $str .= "-";
        }

        $str .= "P";

        if (!$this->y && !$this->m && !$this->d) {
            $str .="0Y";
        }
        
        if ($this->y) {
            $str .= $this->y . "Y";
        }
        if ($this->m) {
            $str .= $this->m . "M";
        }
        if ($this->d) {
            $str .= $this->d . "D";
        }

        if ($this->h || $this->i || $this->s) {
            $str .= "T";
        }

        if ($this->h) {
            $str .= $this->h . "H";
        }
        if ($this->i) {
            $str .= $this->i . "M";
        }
        if ($this->s) {
            $str .= $this->s . "S";
        }
        
        return $str;
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}