<?php
namespace core\Type;
/**
 * Class for unique ids
 */
class Id
    implements \JsonSerializable
{
    
    protected $id;

    /**
     * Construct a new date object
     * @param string $id
     * @param string $prefix
     */
    public function __construct($id=null, $prefix=null)
    {
        if (is_null($prefix)) {
            $prefix = \laabs::getInstanceName() . "_";
        }

        if (is_null($id)) {
            $this->generate($prefix);
        } else {
            $this->set($id);
        }
    }

    /**
     * Generate a new id
     * @param string $prefix
     * 
     * @return string The new id
     */
    public function generate($prefix)
    {
        $this->id = \laabs\uniqid($prefix);
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }

    /**
     * Call value
     * @param string $id The new id to set. If omitted the current value is returned
     * 
     * @return Id
     */
    public function set($id)
    {
        if (!preg_match('#^[A-Za-z0-9_\-]+$#', $id)) {
            throw new \core\Exception('Invalid id \'%1$s\': Ids must contain only alphanumeric, underscores and dashes', 400, null, [$id]);
        }

        $this->id = (string) $id;
    }

    /**
     * Call value
     * @param string $id The new id to set. If omitted the current value is returned
     * 
     * @return Id
     */
    public function __invoke($id=null)
    {
        if ($id) {
            return $this->set($id);
        } else {
            return (string) $this->id;
        }        
    }


}