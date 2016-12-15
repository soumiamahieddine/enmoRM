<?php
/**
 * Class file for Parser definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Parser definitions
 * 
 * @extends \core\Reflection\Service
 */
class Parser
    extends Service
{

    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Constructor of the parser
     * @param string $name      The name of the parser
     * @param string $type      The type of data handled
     * @param string $classname The class of the parser
     * @param object $bundle    The bundle object
     */
    public function __construct($name, $type, $classname, $bundle)
    {
        $uri = LAABS_PARSER . LAABS_URI_SEPARATOR . $type . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $bundle);
    }

    /**
     * Checks if input is defined on the parser
     * @param string $name The name of the input
     * 
     * @return bool
     */
    public function hasInput($name)
    {
        return $this->hasMethod($name);
    }

    /**
     * Get all inputs defined on the parser
     * 
     * @return array An array of all the \core\Reflection\Input objects
     */
    public function getInputs()
    {
        $reflectionMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $inputs = array();
        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isAbstract()
            ) {
                continue;
            }

            $inputs[] = new Input($reflectionMethod->name, $this->name, $this->container);
        }

        return $inputs;
    }

    /**
     * Get a input definition
     * @param string $name The name of the action
     * 
     * @return object the \core\Reflection\Input object
     * 
     * @throws core\Reflection\Exception if the input is unknown or not public
     */
    public function getInput($name)
    {
        if (!$this->hasInput($name)) {
            throw new \Exception("Undefined input '$this->container/$this->name/$name'");
        }

        $input = new Input($name, $this->name, $this->container);

        if (!$input->isPublic()) {
            throw new \Exception("Input '$this->container/$this->name/$name' is not public");
        }

        return $input;
    }

}
