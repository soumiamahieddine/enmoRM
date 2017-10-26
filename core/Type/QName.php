<?php
namespace core\Type;
/**
 * Class for names
 */
class QName
    implements \JsonSerializable
{
    
    protected $qname;

    /**
     * Construct a new name object
     * @param string $qname
     */
    public function __construct($qname)
    {
        if (!preg_match('#^[A-Za-z_][A-Za-z0-9_]*(\/[A-Za-z_][A-Za-z0-9_]*)*$#', $qname)) {
            throw new \core\Exception("Invalid qualified name '$qname': It must start with a name and contain only uri separators and names");
        }

        $this->qname = $qname;
    }

    /**
     * Get the namespace
     * @return string
     */
    public function getNamespace()
    {
        return \laabs\dirname($this->qname);
    }

    /**
     * Get the base name
     * @return string
     */
    public function getBasename()
    {
        return \laabs\basename($this->qname);
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return $this->qname;
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