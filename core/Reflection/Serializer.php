<?php
/**
 * Class file for Serializer definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Serializer definitions
 */
class Serializer
    extends Service
{

    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Constructor of the serializer
     * @param string $name      The name of the serializer
     * @param string $type      The type of data handled
     * @param string $classname The class of the serializer
     * @param object $bundle    The bundle object
     */
    public function __construct($name, $type, $classname, $bundle)
    {
        $uri = LAABS_SERIALIZER . LAABS_URI_SEPARATOR . $type . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $bundle);
    }

    /**
     * Checks if input is defined on the serializer
     * @param string $name The name of the input
     * 
     * @return bool
     */
    public function hasOutput($name)
    {
        return $this->hasMethod($name);
    }

    /**
     * Get all outputs defined on the serializer
     * 
     * @return array An array of all the \core\Reflection\Output objects
     */
    public function getOutputs()
    {
        $reflectionMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $outputs = array();
        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isAbstract()
            ) {
                continue;
            }

            $outputs[] = new Output($reflectionMethod->name, $this->name, $this->container);
        }

        return $outputs;
    }

    /**
     * Get a output definition
     * @param string $name The name of the output
     * 
     * @return object the \core\Reflection\Output object
     * 
     * @throws core\Reflection\Exception if the output is unknown or not public
     */
    public function getOutput($name)
    {
        if (!$this->hasOutput($name)) {
            throw new \Exception("Undefined output '$this->name::$name'");
        }

        $output = new Output($name, $this->name, $this->container);

        if (!$output->isPublic()) {
            throw new \Exception("Output '$name' is not public");
        }

        return $output;
    }

}
