<?php
/**
 * Class file for Containers
 * @package core
 */
namespace core\Reflection;
/**
 * Abstract Class that defines a service container behaviour
 */
class abstractContainer
{
    use \core\ReadonlyTrait;

    /* Constants */

    /* Properties */
    /**
     * The object storage for instances singletons of containers identified by instance names
     * @var array
     * @static
     * @access protected
     */
    protected static $instances;

    /**
     * The local typed name of container (<dependency> or <bundle>)
     * @var string
     */
    public $name;

    /**
     * The uri of container (dependency/<dependency> or bundle)
     * @var string
     */
    public $uri;

    /**
     * The name of the instance
     *  <bundle> or  (<caller>)\dependency\<dependency>, with <caller> = dependency\<dependency> or <bundle>, and so on...
     * @var string
     */
    public $instance;

    /**
     * The configuration section of the container
     * @var core\Configuration\Section
     */
    public $configuration;

    /* Methods */
    /**
     * Getter for component name
     * 
     * @return string The fulle name
     */
    public function getName() 
    {
        return $this->uri;
    }

    /**
     * Getter for component name
     * 
     * @return string The base name without namespace
     */
    public function getShortName() 
    {
        return \laabs\basename($this->uri);
    }

    /**
     * Getter for namespace name
     * 
     * @return string The namesapce uri
     */
    public function getNamespaceName() 
    {
        return \laabs\dirname($this->uri);
    }

    /**
     * Indicates whether the container has a service or not
     * @param string $service The name of the service
     * 
     * @return bool
     */
    public function hasService($service)
    {
        return ($this->getClassName($service) !== null);
    }

    /**
     * Returns a core service object
     * @param string $service The name of the service
     * 
     * @return \core\Reflection\Service The service object
     * 
     * @throws Exception if the service is not declared by the container
     */
    public function getService($service)
    {
        $class = $this->getClassName($service);
        if (!$class) {
            throw new \core\Exception("Undefined service '$this->uri/$service'");
        }

        $service = new Service($service, $class, $this);

        return $service;
    }

    /**
     * Returns an instance of a service
     * @param string $service   The name of the service
     * @param array  $arguments An array of arguments to pass to service
     * 
     * @return object The service instance
     */
    public function callService($service, array $arguments=null)
    {
        $service = $this->getService($service);
        $serviceObject = $service->callArgs($arguments);

        return $serviceObject;
    }

    /**
     * Magic method allowing the call of services by calling a container method
     * @param string $method    The name of the core container method OR the name of the core service to call
     * @param array  $arguments An array of arguments to pass to method or service
     * 
     * @return mixed The result of the container method OR the service instance
     * 
     * @throws Exception if the method is not declared by the container
     */
    public function __call($method, array $arguments=null)
    {
        if (method_exists($this, $method)) {
            return $this->$method($arguments);
        }

        if ($class = $this->getClassName($method)) {
            return $this->callService($method, $arguments);
        }

        throw new \core\Exception("Call to undefined method or service '$this->uri/$method'");
    }

}
