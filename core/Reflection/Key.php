<?php
/**
 * Class file for Key definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Key definitions
 *
 */
class Key
{

    /* Constants */

    /* Properties */
    /**
     * constraint name
     * @var string
     */
    public $name;

    /**
     * constraint type
     * @var string
     */
    public $type;

    /**
     * The declaring class name
     * @var string
     */
    public $class;

    /**
     * key definitions
     * @var array
     */
    public $fields;

    /* Methods */
    /**
     * Constructor of the injection service
     * @param string $name   The name of the holding class
     * @param string $class  The declaring class
     * @param array  $fields The array of fields, i.e. property names
     */
    public function __construct($name, $class, $fields)
    {
        $this->name = $name;

        $this->class = $class;

        $this->type = "UNIQUE";

        $this->fields = $fields;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->class.LAABS_URI_SEPARATOR.$this->name;
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the class
     *
     * @return object
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get the schema
     *
     * @return object
     */
    public function getSchema()
    {
        return strtok($this->class, LAABS_URI_SEPARATOR);
    }

    /**
     * Get all field definitions
     *
     * @return array of \core\Reflection\Property The property definition objects
     */
    public function getFields()
    {
        reset($this->fields);

        return $this->fields;
    }


    /**
     * Get a primary or unique key assert
     *
     * @return object The query assert
     */
    public function getAssert()
    {
        $class = \laabs::getClass($this->class);

        $fields = $this->getFields();

        // Bind first key field
        $field = reset($fields);
        $property = $class->getProperty($field);
        $param = new \core\Language\Param($this->class.LAABS_URI_SEPARATOR.$field);
        $assert = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $property, $param);

        // Bind other keyfields
        while ($field = next($fields)) {
            $property = $class->getProperty($field);
            $param = new \core\Language\Param($this->class.LAABS_URI_SEPARATOR.$field);
            $right = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $property, $param);

            $assert = new \core\Language\LogicalOperation(LAABS_T_AND, $assert, $right);
        }

        return $assert;
    }

    /**
     * Get an object representing the key values
     *  Behaviour depends on the type of key value passed:
     *      - associative array will use array keys
     *      - object will use properties
     *      - scalar value or indexed array will use key field positions
     * @param object $keyValue The key values
     *
     * @return object The data object representing the key
     */
    public function getObject($keyValue)
    {
        $keyObject = new \Stdclass();

        if (is_object($keyValue) && !\laabs::isScalar($keyValue)) {
            $keyValue = get_object_vars($keyValue);
        } elseif (\laabs::isScalar($keyValue)) {
            $keyValue = array($keyValue);
        }

        foreach ($this->getFields() as $pos => $field) {
            $value = null;
            if (isset($keyValue[$field]) && \laabs::isScalar($keyValue[$field])) {
                $value = $keyValue[$field];
            } elseif (isset($keyValue[$pos]) && \laabs::isScalar($keyValue[$pos])) {
                $value = $keyValue[$pos];
            }
            $keyObject->$field = $value;
        }

        return $keyObject;
    }
}
