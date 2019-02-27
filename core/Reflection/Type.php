<?php
/**
 * Class file for object type definitions (classes)
 * @package core
 */
namespace core\Reflection;

/**
 * Class for Type definitions
 * 
 * @extends \core\Reflection\Service
 */
class Type
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
     * Pkey definition
     * @var \core\Reflection\PrimaryKey
     */
    public $pkey;

    /**
     * key definitions
     * @var \core\Reflection\Key[]
     */
    public $key = array();

    /**
     * fkey definitions
     * @var \core\Reflection\ForeignKey[]
     */
    public $fkey = array();

    /**
     * Index definitions
     * @var \core\Reflection\Index[]
     */
    public $index;

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
            if ($typeName = \laabs::getClassName($parentClass->getName())) {
                $this->inherit($typeName);
            }
            $this->extension = \laabs::getClassName($parentClass->name);
        }
        
        if (isset($this->tags['substitution'])) {
            $this->substitution = $this->tags['substitution'][0];
        }

        if (isset($this->tags['pkey'])) {
            $keyInfo = $this->tags['pkey'][0];
            if (preg_match("#(?<name>\w+)?\s*\[(?<fields>[^\)]+)\]#", $keyInfo, $parser)) {

                $name = $parser['name'];
                if (!$name) {
                    $name = "pkey";
                }

                $fields = \laabs\explode(",", $parser['fields']);
                foreach ($fields as $pos => $field) {
                    $fields[$pos] = trim($field);
                }

                $this->pkey = new PrimaryKey($name, $this->uri, $fields);
            }
        }

        if (isset($this->tags['key'])) {
            foreach ($this->tags['key'] as $pos => $key) {
                preg_match("#(?<name>\w+)?\s*\[(?<fields>[^\)]+)\]#", $key, $parser);

                $fields = \laabs\explode(",", $parser['fields']);
                foreach ($fields as $pos => $field) {
                    $fields[$pos] = trim($field);
                }

                $name = $parser['name'];
                if (!$name) {
                     $name = "unique" . LAABS_URI_SEPARATOR . count($this->key);
                }

                $this->key[] = new Key($name, $this->uri, $fields);
            }
        }

        if (isset($this->tags['index'])) {
            foreach ($this->tags['index'] as $pos => $index) {
                preg_match("#(?<name>\w+)?\s*\[(?<fields>[^\)]+)\]#", $index, $parser);

                $fields = \laabs\explode(",", $parser['fields']);
                foreach ($fields as $pos => $field) {
                    $fields[$pos] = trim($field);
                }

                $name = $parser['name'];
                if (!$name) {
                     $name = "index" . LAABS_URI_SEPARATOR . count($this->index);
                }

                $this->index[] = new Index($name, $this->uri, $fields);
            }
        }

        if (isset($this->tags['fkey'])) {
            foreach ($this->tags['fkey'] as $pos => $fkey) {
                preg_match("#(?<name>\w+)?\s*\[(?<fields>[^\)]+)\]\s*(?<refname>[\w\/]+)\s*\[(?<reffields>[^\)]+)\]#", $fkey, $parser);

                $fields = \laabs\explode(",", $parser['fields']);
                foreach ($fields as $pos => $field) {
                    $fields[$pos] = trim($field);
                }

                $reffields = \laabs\explode(",", $parser['reffields']);
                foreach ($reffields as $pos => $reffield) {
                    $refields[$pos] = trim($reffield);
                }

                $refname = $parser['refname'];

                $name = $parser['name'];
                if (!$name) {
                     $name = "fkey" . LAABS_URI_SEPARATOR . count($this->fkey);
                }

                $this->fkey[] = new ForeignKey($name, $this->uri, $fields, $refname, $reffields);
            }
        }

    }

    protected function inherit($typeName)
    {

        $type = \laabs::getClass($typeName);

        if ($parentPKey = $type->getPrimaryKey()) {
            $this->pkey = new PrimaryKey($parentPKey->getName(), $this->uri, $parentPKey->getFields());
        }
        
        $parentKeys = $type->getKeys("UNIQUE");
        foreach ($parentKeys as $parentKey) {
            $this->key[] = new Key($parentKey->getName(), $this->uri, $parentKey->getFields());
        }
        
        $parentFKeys = $type->getForeignKeys();
        foreach ($parentFKeys as $parentFKey) {
            $this->fkey[] = new ForeignKey($parentFKey->getName(), $this->uri, $parentFKey->getFields(), $parentFKey->getRefClass(), $parentFKey->getRefFields());
        }
    }

    /**
     * Instantiate the type object for the service declaration
     * @param array $passedArgs
     * 
     * @return object The new object
     */
    /*public function newInstanceArgs(array $passedArgs=array())
    {
        // Get construction method
        if ($constructor = $this->getConstructor()) {
            $object = parent::newInstanceArgs($passedArgs);
        } else {
            $object = parent::newInstanceWithoutConstructor();        
        }

        return $object;
    }*/

    /**
     * Instantiate the type object for the service declaration
     * 
     * @return object The new object
     */
    /*public function newInstance()
    {
        // Get construction method
        if ($constructor = $this->getConstructor()) {
            $object = parent::newInstanceArgs($passedArgs);
        } else {
            $object = parent::newInstanceWithoutConstructor();        
        }

        return $object;
    }*/

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
     * Check keys
     * @return boolean
     */
    public function hasKey() 
    {
        return (isset($this->pkey) || isset($this->key));
    }
    
    /**
     * Get all key definitions
     * @param string $filter The type of key (primary or unique)
     * 
     * @return array of \core\Reflection\Key The key definition objects
     */
    public function getKeys($filter=false)
    {
        $keys = array();

        if ((!$filter || $filter == "PRIMARY KEY") && $this->pkey) {
            $keys[] = $this->pkey;
        }

        if (!$filter || $filter == "UNIQUE") {
            return array_merge($keys, $this->key);
        }

        return $this->key;
    }

    /**
     * Check primary key
     * @return boolean
     */
    public function hasPrimaryKey() 
    {
        return isset($this->pkey);
    }

    /**
     * Retrieve the Sdo primary Key
     *  @return object the key object
     */
    public function getPrimaryKey() 
    {
        return $this->pkey;
    }

    /* Foreign Key methods */
    /**
     * Retrieve the Sdo Foreign Keys
     *  @param string $refClass filter on reference class (unqualified or qualified name)
     * 
     *  @return array Sdo Foreign Key
     */
    public function getForeignKeys($refClass=null)
    {       
        if ($refClass) {
            $fkeys = array();
            foreach ($this->fkey as $fkey) {
                if ($fkey->refClass == $refClass) {
                    $fkeys[] = $fkey;
                }
            }

            return $fkeys;
        } else {
            return $this->fkey;
        }
        
    }


    /**
     * List properties available on the given data object VS class properties
     * @param object $object The object holding the data 
     *
     * @return array
     * @author 
     */
    public function getObjectProperties($object)
    {
        $properties = $this->getProperties();
        reset($properties);
        $usableProperties = array();
        do {
            $property = current($properties);
            
            $propertyName = $property->getName();

            if (!property_exists($object, $propertyName)) {
                continue;
            }

            if (!$property->isPublic()) {
                continue;
            }
            
            if (!$property->isStringifyable()) {
                continue;
            }
            
            $usableProperties[] = $property;

        } while (next($properties));

        return $usableProperties;
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

    /**
     * Get all index definitions
     * 
     * @return array of \core\Reflection\Index The index definition objects
     */
    public function getIndexes()
    {
        return $this->index;
    }  

}
