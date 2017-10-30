<?php
/**
 * Class file for Reflection Service Method
 * @package core
 */
namespace core\Reflection;
/**
 * Class that defines a service method
 * 
 * @uses \core\ReadonlyTrait
 */
class Method
    extends abstractMethod
{
    /* Constants*/

    /* Properties */
    /**
     * The name of the service container
     * @var string
     */
    public $container;

    /* Methods */
    /**
     * Constructor of the injection service method
     * @param string $method    The name of the method
     * @param string $class     The class of the service that declares the method
     * @param string $container The name of the service container for context transmission to other service calls
     */
    public function __construct($method, $class, $container)
    {
        parent::__construct($class, $method);

        $this->container = $container;
    }


    /**
     * Call the method for a given Service object with an array of parameters
     * Send a LAABS_METHOD_CALL event to observers before call and a LAABS_METHOD_RETURN event to observers after call
     * @param object $serviceObject The service object to call the method from
     * @param array  $args          An indexed or associative array of arguments to be passed to the method call
     * 
     * @return mixed The return of method call
     */
    public function callArgs($serviceObject=null, array $args=null)
    {
        \core\Observer\Dispatcher::notify(LAABS_METHOD_CALL, $this, $args);

        $return = $this->invokeArgs($serviceObject, (array) $args);

        \core\Observer\Dispatcher::notify(LAABS_METHOD_RETURN, $return);

        return $return;
    }

    /**
     * Call the method for a given Service object with optional parameters
     * Send a LAABS_METHOD_CALL event to observers before call and a LAABS_METHOD_RETURN event to observers after call
     * @param object $serviceObject The service object to call the method from
     * 
     * @return mixed The return of method call
     */
    public function call($serviceObject=null)
    {
        $args = func_get_args();
        array_shift($args);

        return $this->callArgs($serviceObject, $args);
    }

    /**
     * Get the value of parameters for the call
     * using the call time parameters, the configuration directives and the default values from method definition
     * @param array  $passedArgs    An indexed or associative array of arguments passed by the caller
     * @param object $configuration A configuration object
     * @param bool   $onlyAssoc     Use passedArgs only if associative array given
     *
     * @return array An associative array of arguments to be used for the service call
     * 
     * @access protected
     */
    public function getCallArgs(array $passedArgs = null, $configuration=null, $onlyAssoc = false)
    {
        $parameters = $this->getParameters();
        $callArgs = array();
        for ($i=0, $l=count($parameters); $i<$l; $i++) {
            $parameter = $parameters[$i];
            $argName = $parameter->name;
            $argType = $parameter->getType();
            $argValue = null;
            // Get value from passed args
            switch(true) {
            
                // 1 - Get value from associative array of passed args
                case isset($passedArgs[$argName]) :
                    $argValue = $passedArgs[$argName];
                    break;
                
                // 2 - Get value from indexed array if onlyAssoc is false
                case !$onlyAssoc && isset($passedArgs[$i]):
                    $argValue = $passedArgs[$i];
                    break;
                
                // 3 - Get value as injection if type is a class
                case $argType && \laabs::isServiceType($argType) :
                    $service = null;
                    $serviceClass = $argType;
                    //$serviceUri = str_replace(LAABS_NS_SEPARATOR, LAABS_URI_SEPARATOR, $serviceClass);

                    $classParser = \laabs::parseClass($serviceClass); 
                    if (array_key_exists(LAABS_DEPENDENCY, $classParser)) {
                        if (\laabs::hasDependency($classParser[LAABS_DEPENDENCY])) { 
                            $dependency = \laabs::dependency($classParser[LAABS_DEPENDENCY], $this->container);
                            $service = $dependency->getService($classParser[LAABS_SERVICE]);
                        }
                    } elseif (array_key_exists(LAABS_BUNDLE, $classParser)) {
                        if (\laabs::hasBundle($classParser[LAABS_BUNDLE])) { 
                            $bundle = \laabs::bundle($classParser[LAABS_BUNDLE]);
                            if (array_key_exists(LAABS_CONTROLLER, $classParser)) {
                                $service = $bundle->getController($classParser[LAABS_CONTROLLER]);
                            } elseif (array_key_exists(LAABS_MODEL, $classParser)) {
                                $service = $bundle->getClass($classParser[LAABS_MODEL]);
                            } elseif (array_key_exists(LAABS_PARSER, $classParser)) {
                                $service = $bundle->getParser($classParser[LAABS_PARSER]);
                            } elseif (array_key_exists(LAABS_SERIALIZER, $classParser)) {
                                $service = $bundle->getSerializer($classParser[LAABS_SERIALIZER]);
                            } elseif (array_key_exists(LAABS_SERVICE, $classParser)) {
                                $service = $bundle->getService($classParser[LAABS_SERVICE]);
                            }
                        }
                    }
                    
                    if ($service) {
                        // Dependency can only receive associative array of arguments from caller
                        if (!is_array($passedArgs) || !\laabs\is_assoc($passedArgs)) {
                            $passedArgs = array();
                        }
                        
                        $argValue = $service->callArgs($passedArgs);
                    }
                    break;
                
                // 4 - Get value from configuration if array or scalar
                case (!$argType || \laabs::isScalarType($argType) || $argType == "array") && isset($configuration[$argName]):
                    $argValue = $configuration[$argName];
                    break;
                    
                // Finally get default value from parameter
                case (!$argType || \laabs::isScalarType($argType) || $argType == "array") && $parameter->isDefaultValueAvailable():
                    $argValue = $parameter->getDefaultValue();
                    break;
            }
            
            //if (!is_null($argValue)) {
                $callArgs[$argName] = $argValue;
            //}
        }

        return $callArgs;
    }

}