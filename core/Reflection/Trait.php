<?php
namespace core\Reflection;

/**
 * Class for trait/data type definitions
 * 
 * @extends \core\Reflection\Service
 */
class Trait
    extends Service
{

    /* Constants */

    /* Properties */
    /**
     * @var string
     */
    public $extension;

    /* Methods */
    /**
     * Constructor of the injection service
     * @param string $uri       The uri of the service
     * @param string $class     The class of the service
     * @param object $container The service container object
     */
    public function __construct($uri, $class, $container)
    {
        parent::__construct($uri, $class, $container);

    }

    /**
     * Parse self and parent class doc comment recursively
     */
    protected function parseDocComment()
    {
        if ($parentClass = $this->getParentClass()) {
            $this->importDocComments($parentClass->getName());
            $this->extension = \laabs::getClassName($parentClass->name);
        }
    }

    protected function importDocComments($className)
    {
        $traitName = \laabs::getClassName($className);
        $type = \laabs::getClass($traitName);
    }

    /** 
     * Get the parent type name
     * @return string
     */
    public function getExtensionName()
    {
        return $this->extension;
    }

    
    /**
     * Get the schema (bundle)
     * @return \core\Reflection\bundle
     */
    public function getSchema()
    {
        return $this->container;
    }

    /**
     * Get a property definition
     * @param string $name The name of the property to get
     * 
     * @return \core\Reflection\Property The property definition object
     */
    public function getProperty($name)
    {
        $rProperty = parent::getProperty($name);
        
        return new Property($rProperty->name, $this->name, $this);
    }

    /**
     * Get all property definitions
     * @param int $filter A bitmask of property modifiers
     * 
     * @return array of \core\Reflection\Property The property definition objects
     */
    public function getProperties($filter=null)
    {
        $properties = array();

        foreach ((array) parent::getProperties() as $rProperty) {
            $properties[] = new Property($rProperty->name, $this->name, $this);
        }

        return $properties;
    }

}
