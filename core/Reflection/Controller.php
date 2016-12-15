<?php
/**
 * Class file for Controller definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Controller definitions
 * 
 * @extends \core\Reflection\Service
 */
class Controller
    extends Service
{

    /* Constants */

    /* Properties */
    

    /* Methods */
    
    /**
     * Constructor of the controller
     * @param string $name      The name of the controller
     * @param string $classname The class of the controller
     * @param object $bundle    The bundle object
     */
    public function __construct($name, $classname, $bundle)
    {
        $uri = LAABS_CONTROLLER . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $bundle);
    }

    /**
     * Checks if action is defined on the controller
     * @param string $name The name of the action
     * 
     * @return bool
     */
    public function hasAction($name)
    {
        return $this->hasMethod($name);
    }

    /**
     * Get all actions defined on the controller
     * 
     * @return array An array of all the \core\Reflection\Action objects
     */
    public function getActions()
    {
        $reflectionMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $actions = array();
        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isAbstract()
            ) {
                continue;
            }

            $actions[] = new Action($reflectionMethod->name, $this->name, $this->container);
        }

        return $actions;
    }

    /**
     * Get a controller definition
     * @param string $name The name of the action
     * 
     * @return object the \core\Reflection\Action object
     * 
     * @throws core\Reflection\Exception if the action is unknown or not public
     */
    public function getAction($name)
    {
        if (!$this->hasAction($name)) {
            throw new \Exception("Undefined action '$this->container/$this->name/$name'");
        }

        $action = new Action($name, $this->name, $this->container);

        if (!$action->isPublic()) {
            throw new \Exception("Action '$name' is not public");
        }

        return $action;
    }

}
