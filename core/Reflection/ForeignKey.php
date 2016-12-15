<?php
/**
 * Class file for PK definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for PK definitions
 * 
 */
class ForeignKey
    extends Key
{

    /* Constants */

    /* Properties */
    /**
     * @var string reference
     */
    public $refClass;

    /**
     * @var array refFields
     */
    public $refFields = array();

    /* Methods */
    /**
     * Constructor of the injection service
     * @param string $name      The uri of the service     
     * @param string $class     The declaring class
     * @param array  $fields    The array of fields, i.e. property names
     * @param string $refClass  The name of reference class
     * @param array  $refFields The array of ref fields, i.e. property names
     */
    public function __construct($name, $class, $fields, $refClass, $refFields)
    {
        parent::__construct($name, $class, $fields);
        
        $this->type = "FOREIGN KEY";

        $this->refClass = $refClass;

        $this->refFields = $refFields;
    }

    /**
     * Get the ref class
     * 
     * @return object
     */
    public function getRefClass()
    {
        return $this->refClass;
    } 

    /**
     * Get all ref field definitions
     * 
     * @return array of \core\Reflection\Property The property definition objects
     */
    public function getRefFields()
    {
        reset($this->refFields);
        
        return $this->refFields;
    }

    /**
     * Get a foreign key assert for parent to child navigation
     *
     * @return object The query assert
     */
    public function getChildAssert()
    {
        $class = \laabs::getClass($this->class);

        foreach ($this->getFields() as $keyField) {
            $keyProperties[] = $class->getProperty($keyField);
        }
        
        $refFields = $this->getRefFields();
        
        $keyProperty = reset($keyProperties);
        $refField = reset($refFields);

        $param = new \core\Language\Param($this->refClass . LAABS_URI_SEPARATOR . $refField);
        $assert = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $keyProperty, $param);
        
        while ($refField = next($refFields) && $keyProperty = next($keyProperties)) {
            $param = new \core\Language\Param($this->refClass . LAABS_URI_SEPARATOR . $refField);
            $right = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $keyProperty, $param);
            
            $assert = new \core\Language\LogicalOperation(LAABS_T_AND, $assert, $right);
        }

        return $assert; 
    }

    /**
     * Get an object representing the ref key values for a parent to child navigation
     *  Behaviour depends on the type of key value passed:
     *      - associative array will use array keys
     *      - object will use properties
     *      - scalar value or indexed array will use key field positions
     * @param mixed $parentValue The available parent values
     *
     * @return object An object holding the key values to use for binding on operation
     */
    public function getParentObject($parentValue)
    {
        $childObject = new \Stdclass();
        
        if (is_object($parentValue) && !\laabs::isScalar($parentValue)) {
            $parentValue = get_object_vars($parentValue);
        } elseif (\laabs::isScalar($parentValue)) {
            $parentValue = array($parentValue);
        }

        $refFields = $this->getRefFields();
        foreach ($refFields as $pos => $refField) {        
            $value = null;
            if (isset($parentValue[$refField]) && \laabs::isScalar($parentValue[$refField])) {
                $value = $parentValue[$refField];
            } elseif (isset($parentValue[$pos]) && \laabs::isScalar($parentValue[$pos])) {
                $value = $parentValue[$pos];
            }
            
            $childObject->$refField = $value;
        }
        
        return $childObject;
    }

    /**
     * Get a foreign key reference assert
     *
     * @return object The query assert
     */
    public function getParentAssert()
    {
        $refProperties = array();
        $refClass = \laabs::getClass($this->getRefClass());
        foreach ($this->getRefFields() as $refField) {
            $refProperties[] = $refClass->getProperty($refField);
        }

        $keyFields = $this->getFields();

        $refProperty = reset($refProperties);
        $keyField = reset($keyFields);

        $param = new \core\Language\Param($this->class . LAABS_URI_SEPARATOR . $keyField);
        $assert = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $refProperty, $param);
        
        // Add other asserts with 'AND' logical operator
        while ($refProperty = next($refProperties) && $keyField = next($keyFields)) {
            $param = new \core\Language\Param($this->class . LAABS_URI_SEPARATOR . $keyField);
            $right = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $refProperty, $param);
            
            $assert = new \core\Language\LogicalOperation(LAABS_T_AND, $assert, $right);
        }
        
        return $assert;
    }

    /**
     * Get an object representing the child key values for a child to parent navigation
     *  Behaviour depends on the type of key value passed:
     *      - associative array will use array keys
     *      - object will use properties
     *      - scalar value or indexed array will use key field positions
     * @param mixed $childValue The available child values
     *
     * @return object An object holding the key values to use for binding on operation
     * @access public
     */
    public function getChildObject($childValue)
    {
        $parentObject = new \Stdclass();
        
        if (is_object($childValue) && !\laabs::isScalar($childValue)) {
            $childValue = get_object_vars($childValue);
        } elseif (\laabs::isScalar($childValue)) {
            $childValue = array($childValue);
        }
        
        $keyFields = $this->getFields();

        foreach ($keyFields as $pos => $keyField) {           
            $value = null;
            if (isset($childValue[$keyField]) && \laabs::isScalar($childValue[$keyField])) {
                $value = $childValue[$keyField];
            } elseif (isset($childValue[$pos]) && \laabs::isScalar($childValue[$pos])) {
                $value = $childValue[$pos];
            }

            $parentObject->$keyField = $value;
        }
        
        return $parentObject;
    }

}
