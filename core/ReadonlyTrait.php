<?php
/**
 * File that declares Trait Readonly
 * Brings magic method __set that triggers error, forcing the use of setter methods
 *
 */
namespace core;
/**
 * Trait Readonly
 * Brings magic method __set that triggers error, forcing the use of setter methods
 * @package core
 */
trait ReadonlyTrait
{
    /**
     * Magic method __set that triggers error, forcing the use of setter methods
     * @param string $name  The name of the property
     * @param mixed  $value The new value of the property
     * 
     * @return void
     * 
     * @access public
     */
    public function __set($name, $value=null)
    {
        if (!in_array($name, get_object_vars($this))) {
            trigger_error("Undeclared property " . __CLASS__ . "::" . $name);
        }

        trigger_error("Can not modify read only property " . __CLASS__ . "::" . $name);
    }

}

