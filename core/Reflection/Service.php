<?php
/**
 * Class file for Service
 * @package core
 */
namespace core\Reflection;
/**
 * Class that defines a service
 * 
 * @uses \core\ReadonlyTrait
 */
class Service
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
     * The uri of the container instance holding the service
     * @var string
     */
    public $container;

    /**
     * The configuration section of the service
     * @var core\Configuration\Section
     */
    public $configuration;

    /**
     * The methods cache
     * @var array
     */
    public $methods = array();

    /* Methods */
    /**
     * Constructor of the injection service
     * @param string $uri       The uri of the service
     * @param string $classname The class of the service
     * @param object $container The service container object
     */
    public function __construct($uri, $classname, $container)
    {
        $this->uri = $uri;

        $this->container = $container->instance;

        parent::__construct($classname);

        if ($container->configuration) {
            $this->configuration = clone($container->configuration);
        } else {
            $this->configuration = new \core\Configuration\Section();
        }
    }

    /**
     * Returns the service methods
     * @param int $filter
     * 
     * @return array An array of Method objects declared for the service
     */
    public function getMethods($filter=null)
    {
        if (empty($this->methods)) {

            $reflectionMethods = parent::getMethods(Method::IS_PUBLIC & ~Method::IS_STATIC);

            for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
                $reflectionMethod = $reflectionMethods[$i];
                if ($reflectionMethod->isConstructor()
                    || $reflectionMethod->isDestructor()
                ) {
                    continue;
                }
                $this->methods[$reflectionMethod->name] = new Method($reflectionMethod->name, $this->name, $this->container);
            }
        }

        return $this->methods;
    }

    /**
     * Get a service method declaration from its name
     * @param string $name The name of the method
     * 
     * @return object The Method object
     * 
     * @throws Exception if the method is not declared by the service
     */
    public function getMethod($name)
    {
        if (!$this->hasMethod($name)) {
            throw new \core\Exception("Undefined method '$this->container/$this->component/$name'");
        }

        $method = new Method($name, $this->name, $this->container);

        if (!$method->isPublic()) {
            throw new \core\Exception("Method '$name' is not public");
        }

        return $method;
    }

    /**
     * Get a service construction method declaration from its name
     * 
     * @return object The construction method object
     * 
     * @throws Exception if the method is not declared by the service
     */
    public function getConstructor()
    {
        if (($constructor = parent::getConstructor()) && $constructor->isPublic()) {
            return $this->getMethod($constructor->name);
        }
    }

    /**
     * Call the service with an array of parameters
     * Send a LAABS_SERVICE_CALL before call and a LAABS_SERVICE_OBJECT after call to observers
     * param array $passedArgs An indexed or associative array of arguments to be passed to the service
     * 
     * @return object The Service object
     * 
     * @see newInstance();
     */
    public function call()
    {
        $callArgs = func_get_args();
        
        return $this->callArgs($callArgs);
    }

    /**
     * Call the service with parameters
     * Send a LAABS_SERVICE_CALL before call and a LAABS_SERVICE_OBJECT after call to observers
     * @param array $passedArgs An indexed or associative array of arguments to be passed to the service
     * 
     * @return object The Service object
     * 
     * @see newInstance();
     */
    public function callArgs(array $passedArgs=null)
    {
        \core\Observer\Dispatcher::notify(LAABS_SERVICE_INJECTION, $this, $passedArgs);

        $serviceObject = $this->newInstance($passedArgs);

        \core\Observer\Dispatcher::notify(LAABS_SERVICE_OBJECT, $serviceObject);

        return $serviceObject;
    }

    /**
     * Instantiate the service object for the service declaration
     * @param array $passedArgs An indexed or associative array of arguments to be passed to the service
     * 
     * @return object The service object
     */
    public function newInstance($passedArgs = null, ...$args)
    {
        // Get construction method
        if ($this->hasConstructor()) {
            $constructor = $this->getConstructor();
            $constructorArgs = $constructor->getCallArgs($passedArgs, $this->configuration);

            $serviceObject = parent::newInstanceArgs($constructorArgs);
        } elseif ($this->hasMethod($this->getShortName()) && ($method = $this->getMethod($this->getShortName())) && $method->isStatic() && $method->isPublic()) {
            
            $staticFactory = $this->getMethod($this->getShortName());
            $staticFactoryArgs = $staticFactory->getCallArgs($passedArgs, $this->configuration);
            $serviceObject = $staticFactory->callArgs(null, $staticFactoryArgs);
        } else {
            $serviceObject = parent::newInstanceWithoutConstructor();
        }

        $this->useTraits($serviceObject, $passedArgs);
        
        return $serviceObject;
    }

    /**
     * Triggers the use of traits for the service
     * If trait has a method that has the same name, the method will be called
     * @param object $serviceObject The service object
     * @param array  $passedArgs    An indexed or associative array of arguments to be passed to the trait invocation method
     * 
     * @return void
     * 
     * @access protected
     */
    protected function useTraits($serviceObject, $passedArgs)
    {
        foreach (\laabs\class_uses($serviceObject) as $trait) {
            $traitName = \laabs\basename($trait);
            $traitOrigin = strtok($trait, LAABS_NS_SEPARATOR);
            if ($traitOrigin == LAABS_DEPENDENCY && method_exists($serviceObject, $traitName)) {
                $useMethod = new Method($traitName, $trait, $this->container);
                $callArgs = $useMethod->getCallArgs($passedArgs, $this->configuration, $onlyAssoc = true);

                if ($useMethod->isStatic()) {
                    call_user_func_array($this->name . "::" . $traitName, $callArgs);
                } else {
                    call_user_func_array(array($serviceObject, $traitName), $callArgs);
                }
            }
        }
    }

}