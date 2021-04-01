<?php
/**
 * Class file for object Message type definitions (classes)
 * @package core
 */
namespace core\Reflection;

/**
 * Class for Message Type definitions
 * 
 */
class Message
    extends abstractClass
{
    /* Constants */

    /* Properties */
    /**
     * Class uri
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string
     */
    public $substitution;

    /**
     * Properties cache
     * @var array
     */
    public $properties = array();

    /* Methods */
    /**
     * Constructor of the injection service
     * @param string $name      The uri of the service
     * @param string $classname The class of the service
     * @param object $bundle    The service container object
     */
    public function __construct($name, $classname, $bundle)
    {
        $this->uri = $bundle->getName() . LAABS_URI_SEPARATOR . $name;

        parent::__construct($classname);

        if ($parentClass = $this->getParentClass()) {
            //$this->importDocComments($parentClass->getName());
            $this->extension = \laabs::getClassName($parentClass->name);
        }
        
        $docComment = $this->getDocComment();

        if (isset($this->tags['substitution'])) {
            $this->substitution = $this->tags['substitution'][0];
        }

    }

    /**
     * Instantiate the type object for the service declaration
     * @param array $passedArgs An indexed or associative array of arguments to be passed to the service
     * 
     * @return object The new object
     */
    public function newInstance($passedArgs=null, ...$args)
    {
        // Get construction method
        if ($constructor = $this->getConstructor()) {
            $object = parent::newInstanceArgs($passedArgs);
        } else {
            $object = parent::newInstanceWithoutConstructor();        
        }

        return $object;
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
     * Get the base type name
     * @return string
     */
    public function getBaseName()
    {
        if ($this->substitution) {
            $baseType = \laabs::getClass($this->substitution);

            return $baseType->getBaseName();
        }

        return $this->uri;
    }

    /** 
     * Get the base type
     * @return type
     */
    public function getBaseType()
    {
        if ($this->substitution) {
            $baseType = \laabs::getClass($this->substitution);

            return $baseType->getBaseType();
        }

        return $this;
    }

    /**
     * Get the fully qualified name
     * @return string
     */
    public function getName()
    {
        return $this->uri;
    }

    /**
     * Get the schema (bundle)
     * @return \core\Reflection\bundle
     */
    public function getSchema()
    {
        return \laabs\dirname($this->uri);
    }

    /**
     * Get a property definition
     * @param string $name The name of the property to get
     * 
     * @return \core\Reflection\Property The property definition object
     */
    public function getProperty($name)
    {
        if (empty($this->properties)) {
            $rProperty = parent::getProperty($name);
        
            $property = new Property($rProperty->name, $this->name, $this);
        } else {
            $property = $this->properties[$name];
        }

        return $property;
    }

    /**
     * Get all property definitions
     * @param int $filter A bitmask of property modifiers
     * 
     * @return array of \core\Reflection\Property The property definition objects
     */
    public function getProperties($filter=null)
    {
        
        if (empty($this->properties)) {
            foreach ((array) parent::getProperties() as $rProperty) {
                $this->properties[$rProperty->name] = new Property($rProperty->name, $this->name, $this);
            }
        }

        return $this->properties;
    }


    /**
     * Checks if the type is a descendant of another class
     * @param string $typename
     * 
     * @return boolean
     */
    public function isSubtypeOf($typename)
    {
        $ancestor = \laabs::getClass($typename);

        return $this->isSubclassOf($ancestor->name);
    }  

}
