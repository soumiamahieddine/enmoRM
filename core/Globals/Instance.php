<?php

namespace core\Globals;

class Instance
    extends AbstractGlobal
{
    /* Constants */
    const NAME = 'INSTANCE';

    /* Properties */

    /* Methods */
    public static function start($init = false) 
    {
        \core\Instance::start();

        if ($init) {
            self::init();
        }
    }

    public static function init()
    {
        $GLOBALS['INSTANCE'] = array();
    }

    public static function setClass($class)
    {
        $GLOBALS['INSTANCE']['laabs']['class'][$class->getName()] = $class;
        $GLOBALS['INSTANCE']['laabs']['property'][$class->getName()] = $class->getProperties();
    }

    public static function getClass($classname)
    {
        if (isset($GLOBALS['INSTANCE']['laabs']['class'][$classname])) {
            return $GLOBALS['INSTANCE']['laabs']['class'][$classname];
        }
    }

    public static function hasClass($classname)
    {
        return (isset($GLOBALS['INSTANCE']['laabs']['class'][$classname]));
    }

    public static function getProperty($classname, $propertyname)
    {
        if (isset($GLOBALS['INSTANCE']['laabs']['property'][$classname][$propertyname])) {
            return $GLOBALS['INSTANCE']['laabs']['property'][$classname][$propertyname];
        }
    }

    public static function hasProperties($classname)
    {
        return (isset($GLOBALS['INSTANCE']['laabs']['property'][$classname]));
    }

    public static function getProperties($classname)
    {
        if (isset($GLOBALS['INSTANCE']['laabs']['property'][$classname])) {
            return $GLOBALS['INSTANCE']['laabs']['property'][$classname];
        }
    }
}