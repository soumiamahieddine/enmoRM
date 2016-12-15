<?php
/**
 * Class file for Interface definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for reflection API
 * 
 * @extends \core\Reflection\Service
 */
class API
    extends abstractClass
{
    
    /* Constants */

    /* Properties */
    /**
     * The uri of the service
     * @var string
     */
    public $uri;

    /**
     * The container domain
     * @var string
     */
    public $domain;

    /* Methods */
    /**
     * Constructor of the API
     * @param string $name      The name of the API
     * @param string $class     The class of the API
     * @param object $container The API container object
     */
    public function __construct($name, $class, $container)
    {
        $this->uri = $container->getName() . LAABS_URI_SEPARATOR . \laabs\basename($name, LAABS_INTERFACE);

        $this->domain = $container->instance;

        parent::__construct($class); 
    }

    /**
     * Getter for component name
     * 
     * @return string The value of the property
     */
    public function getName() 
    {
        return $this->uri;
    }

    /**
     * Getter for component name
     * 
     * @return string The value of the property
     */
    public function getShortName() 
    {
        return \laabs\basename($this->uri);
    }

    /**
     * Check if path exists
     * @param string $name the name of the path
     * 
     * @return boolean
     */
    public function hasPath($name)
    {
        return parent::hasMethod($name);
    }

    /**
     * Returns the API paths
     * @param int $filter
     * 
     * @return array An array of path objects declared for the API
     */
    public function getPaths($filter=null)
    {
        $reflectionMethods = parent::getMethods(Method::IS_PUBLIC & ~Method::IS_STATIC);
        $paths = array();

        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];

            $paths[] = new Path($reflectionMethod->name, $this->name, $this->domain);
        }

        return $paths;
    }

    /**
     * Get a API path declaration from its name
     * @param string $name The name of the path
     * 
     * @return object The Method object
     * 
     * @throws Exception if the path is not declared by the API
     */
    public function getPath($name)
    {
        if (!parent::hasMethod($name)) {
            throw new \core\Exception("Undefined path '$this->uri/$name'");
        }

        $path = new Path($name, $this->name, $this->domain);

        if (!$path->isPublic()) {
            throw new \core\Exception("Path '$name' is not public");
        }

        return $path;
    }
}
