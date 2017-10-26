<?php
/**
 * Class file for observer definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for reflection observer
 * 
 * @extends \core\Reflection\Service
 */
class Observer
    extends Service
{

    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Constructor of the controllere
     * @param string $name      The name of the controller
     * @param string $classname The class of the controller
     * @param object $bundle    The bundle object
     */
    public function __construct($name, $classname, $bundle)
    {
        $uri = LAABS_OBSERVER . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $bundle);
    }

    /**
     * Getter for component name
     * 
     * @return string The value of the property
     */
    public function getName() 
    {
        return $this->component;
    }

    /**
     * Check if handler exists
     * @param string $name the name of the handler
     * 
     * @return boolean
     */
    public function hasHandler($name)
    {
        return parent::hasMethod($name);
    }

    /**
     * Returns the observer handlers
     * @param int $filter
     * 
     * @return array An array of handler objects declared for the observer
     */
    public function getHandlers($filter=null)
    {
        $reflectionMethods = parent::getMethods(Method::IS_PUBLIC & ~Method::IS_STATIC);
        $handlers = array();

        foreach ($reflectionMethods as $name => $reflectionMethod) {
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || !preg_match("#@subject (?<subject>[^\s]+)#", $reflectionMethod->getDocComment())
            ) {
                continue;
            }

            $handlers[] = new Handler($reflectionMethod->name, $this->name, $this->container);
        }

        return $handlers;
    }

    /**
     * Get a observer handler declaration from its name
     * @param string $name The name of the handler
     * 
     * @return object The Method object
     * 
     * @throws Exception if the handler is not declared by the observer
     */
    public function getHandler($name)
    {
        if (!parent::hasMethod($name)) {
            throw new \core\Exception("Undefined handler '$this->container/$this->component/$name'");
        }

        $handler = new Handler($name, $this->name, $this->container);

        if (!$handler->isPublic()
            || $handler->isConstructor()
            || $handler->isDestructor()
            || $handler->isStatic()
            || !$handler->subject
        ) {
            throw new \core\Exception("Method '$name' is not a valid observer handler");
        }

        return $handler;
    }
}
