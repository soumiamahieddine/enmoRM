<?php
namespace core\Type;
/**
 * Class for names
 */
class Name
    implements \JsonSerializable
{
    
    protected $name;

    /**
     * Construct a new name object
     * @param string $name
     */
    public function __construct($name)
    {
        if (!preg_match('#^[A-Za-z0-9_\-]*$#', $name)) {
            throw new \core\Exception("The value can only contain alphanumeric characters, underscores and dashes");
        }

        $this->name = $name;
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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