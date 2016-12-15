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
class PrimaryKey
    extends Key
{

    /* Constants */

    /* Properties */


    /* Methods */
    /**
     * Constructor of the injection service
     * @param string $name   The uri of the service     
     * @param string $class  The declaring class
     * @param array  $fields The array of fields, i.e. property names
     */
    public function __construct($name, $class, $fields)
    {
        parent::__construct($name, $class, $fields);
        
        $this->type = "PRIMARY KEY";
    }

}
