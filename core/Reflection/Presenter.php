<?php
/**
 * Class file for Presenter definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Presenter definitions
 * 
 * @extends \core\Reflection\Service
 */
class Presenter
    extends Service
{

    /* Constants */

    /* Properties */
    

    /* Methods */
    
    /**
     * Constructor of the presenter
     * @param string $name         The name of the presenter
     * @param string $classname    The class of the presenter
     * @param object $presentation The presentation object
     */
    public function __construct($name, $classname, $presentation)
    {
        $uri = LAABS_PRESENTER . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $presentation);
    }

    /**
     * Checks if view is defined on the presenter
     * @param string $name The name of the view
     * 
     * @return bool
     */
    public function hasView($name)
    {
        return $this->hasMethod($name);
    }

    /**
     * Get all views defined on the presenter
     * 
     * @return array An array of all the \core\Reflection\View objects
     */
    public function getViews()
    {
        $reflectionMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $views = array();
        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isAbstract()
            ) {
                continue;
            }

            $views[] = new View($reflectionMethod->name, $this->name, $this->container);
        }

        return $views;
    }

    /**
     * Get a presenter definition
     * @param string $name The name of the view
     * 
     * @return object the \core\Reflection\View object
     * 
     * @throws core\Reflection\Exception if the view is unknown or not public
     */
    public function getView($name)
    {
        if (!$this->hasView($name)) {
            throw new \Exception("Undefined view '$this->container/$this->name/$name'");
        }

        $view = new View($name, $this->name, $this->container);

        if (!$view->isPublic()) {
            throw new \Exception("View '$name' is not public");
        }

        return $view;
    }

}
