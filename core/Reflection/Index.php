<?php
/**
 * Class file for Index definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Index definitions
 * 
 */
class Index
{

    /* Constants */

    /* Properties */
    /**
     * Index name
     * @var string
     */
    public $name;

    /**
     * The declaring class name
     * @var string
     */
    public $class;

    /**
     * index definition
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

        $this->fields = $fields;
    }

    /**
     * Get the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->class . LAABS_URI_SEPARATOR . $this->name;
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

}
