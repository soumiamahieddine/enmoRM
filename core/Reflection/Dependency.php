<?php
/**
 * Class file for Dependency container
 * @package core
 */
namespace core\Reflection;
/**
 * Class for Dependency container
 * 
 * @uses \core\ReadonlyTrait
 */
class Dependency
    extends abstractContainer
{
    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Get a dependency instance singleton.
     * The instance singleton is identified by the name of dependency as well as the instance name of the caller
     * @param string $name   The name of the dependency to get instance of
     * @param object $caller The caller instance
     * 
     * @return \core\Reflection\Dependency object
     */
    public static function getInstance($name, $caller=null)
    {
        // Create unique instance name
        $instance = LAABS_DEPENDENCY . LAABS_URI_SEPARATOR . $name;
        if ($caller) {
            if ($caller->uri != $instance) {
                $instance = $caller->instance . LAABS_URI_SEPARATOR . $instance;
            } else {
                $instance = $caller->instance;
            }
        }

        self::$instances[$instance] = new dependency($instance, $caller);
       
        return self::$instances[$instance];
    }

    /**
     * Constructs a new dependency instance
     * @param string $instance The instance name that identifies singleton
     * @param object $caller   The caller instance object
     * 
     * @return void
     * 
     * @access protected
     */
    protected function __construct($instance, $caller=null)
    {
        $this->name = \laabs\basename($instance);

        $this->uri = LAABS_DEPENDENCY . LAABS_URI_SEPARATOR . $this->name;

        $this->instance = $instance;

        if (!is_dir('..' . DIRECTORY_SEPARATOR . LAABS_DEPENDENCY . DIRECTORY_SEPARATOR . $this->name)) {
            throw new \core\Exception\NotFoundException("Dependency '$this->name' not found");
        }

        /* Load Configuration */
        $dependencyConfig = \laabs::configuration($this->uri);
        if ($dependencyConfig) {
            $this->configuration = clone($dependencyConfig);
        } else {
            $this->configuration = new \core\Configuration\Section();
        }

        /* Use caller instance configuration */
        if ($caller && $caller->configuration) {
            if ($caller->uri == $this->uri) {
                $this->configuration->import($caller->configuration->export());
            } else {
                $this->configuration->import($caller->configuration->export($this->uri));
            }
        }
    }

    /* Service */
    /**
     * Returns the class for a service from its name
     * Service can use an adapter so the namespace differs from the dependency+service names
     * @param string $uri The uri of the service
     * 
     * @return string The class of the service, may use an adapter
     */
    public function getClassName($uri)
    {
        $class = str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);
        $qclass = LAABS_DEPENDENCY . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $class;

        // Class is an interface -> get real class to instantiate by removing Interface suffix
        if (interface_exists($qclass) 
            || interface_exists($qclass . LAABS_INTERFACE) 
            || preg_match('#^.+' . LAABS_INTERFACE . '$#', $class)) {
            if (preg_match('#^.+' . LAABS_INTERFACE . '$#', $class)) {
                $class = substr($class, 0, -(strlen(LAABS_INTERFACE)));
                $uri = substr($uri, 0, -(strlen(LAABS_INTERFACE)));
            }
            
            $adapter = $this->getServiceAdapter($uri);
            if (!$adapter) {
                throw new \core\Exception("Can not instanciate service $uri: missing @Adapter keyword in configuration");
            }

            $adaptedClass = LAABS_DEPENDENCY . LAABS_NS_SEPARATOR 
                . $this->name . LAABS_NS_SEPARATOR 
                . LAABS_ADAPTER . LAABS_NS_SEPARATOR 
                . $adapter . LAABS_NS_SEPARATOR 
                . $class;

            if (!class_exists($adaptedClass)) {
                throw new \core\Exception("Can not instanciate service $uri: Adapter $adapter not found for dependency $this->name");
            }
            
            return $adaptedClass;
        }
        

        if (!class_exists($qclass)) {
            throw new \core\Exception("Can not instanciate service $uri: Service not found");
        }

        return $qclass;
    }

    /**
     * Returns the adapter to use for a given service, as defined on configuration with the keyword "@Adapter"
     * @param string $uri The uri of the service
     * 
     * @return string The name adapter
     */
    public function getServiceAdapter($uri)
    {
        $serviceConfig = str_replace(LAABS_URI_SEPARATOR, ".", $uri);

        if (isset($this->configuration[$serviceConfig . ".@" . LAABS_ADAPTER])) {
            return $this->configuration[$serviceConfig . ".@" . LAABS_ADAPTER];
        }

        if (isset($this->configuration["@".LAABS_ADAPTER])) {
            return $this->configuration["@".LAABS_ADAPTER];
        }
    }

    /**
     * Returns the available adapters for a given service interface
     * @param string $uri The uri of the service
     * 
     * @return array The name of the adapters
     */
    public function getAdapters($uri)
    {
        $adapters = array();

        $serviceFile = str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $uri);

        /* Search path in base path */
        $baseDir = ".." . DIRECTORY_SEPARATOR . LAABS_DEPENDENCY . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . LAABS_ADAPTER;
        if (is_dir($baseDir)) {
            foreach (scandir($baseDir) as $entry) {
                if ($entry == "." || $entry == "..") {
                    continue;
                }
                $path = $baseDir . DIRECTORY_SEPARATOR . $entry;
                if (!is_dir($path)) {
                    continue;
                }
                if (is_file($path . DIRECTORY_SEPARATOR . $serviceFile . ".php")) {
                    $adapters[] = $entry;
                }
            }
        }

        return $adapters;
    }

}
