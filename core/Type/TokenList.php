<?php
namespace core\Type;
/**
 * Class for token lists
 */
class TokenList
    extends \ArrayObject
    implements \JsonSerializable
{
    
    protected $separator;

    /**
     * Construct a new name object
     * @param mixed  $values    A string of separated tokens or an array of tokens
     * @param string $separator The separator character
     */
    public function __construct($values=null, $separator=null)
    {
        if (is_null($separator)) {
            $separator = " ";
        }
        $this->separator = $separator;

        if (!is_null($values)) {
            if (is_string($values)) {
                $values = \laabs\explode($separator, $values);
            }
        } else {
            $values = array();
        }

        parent::__construct($values);
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return \laabs\implode(" ", parent::getArrayCopy());
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }

    /**
     * Check if list contains the value
     * @param mixed $value
     * 
     * @return bool
     */
    public function contains($value)
    {
        return in_array($value, $this->getArrayCopy());
    }
}