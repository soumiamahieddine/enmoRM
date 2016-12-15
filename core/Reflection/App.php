<?php
/**
 * Class file for App definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for App definition
 * 
 */
class App
    extends abstractContainer
{

    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Get a App instance singleton.
     * 
     * @return \core\Reflection\App object
     */
    public static function getInstance()
    {
        if (!isset(self::$instances[0])) {
            self::$instances[0] = new App();
        }

        return self::$instances[0];
    }

    /**
     * Constructs a new app
     * 
     * @return void
     * 
     * @access protected
     */
    protected function __construct()
    {
        $this->name = \laabs::getApp();

        $this->uri = LAABS_APP . LAABS_URI_SEPARATOR . $this->name;

        $this->instance = LAABS_APP . LAABS_URI_SEPARATOR . $this->name;

        $configuration = \core\Configuration\Configuration::getInstance();
        $this->configuration = $configuration->export();
        $this->configuration->import($configuration->export($this->uri));
    }

    /* Routines */
    
    /**
     * Return the extended path for a given filename
     * @param string $uri The uri of resource to find on the bundle. Can be a simple local name or a qualified name
     * 
     * @return string The full qualified extended path
     */
    public function getPath($uri)
    {
        //$path = ".." . DIRECTORY_SEPARATOR 
        $path = LAABS_APP . DIRECTORY_SEPARATOR 
                . $this->name . DIRECTORY_SEPARATOR 
                . str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $uri);

        $extendedPath = Extensions::extendedPath($path);
        
        return $extendedPath;
    }

    /* Presenter */
    
    /* Resource */
    /**
     * Checks if a resource is available on the bundle
     * @param string $name The name/uri of the resource
     * 
     * @return bool
     */
    public function hasResource($name)
    {
        $uri = LAABS_RESOURCE . LAABS_URI_SEPARATOR . $name;

        return (($this->getPath($uri)) != false);
    }

    /**
     * Get a ressource definition
     * @param string $name The name of the resource
     * 
     * @return object the \core\Reflection\Resource object
     * @throws core\Reflection\Exception if the resource is unknown
     */
    public function getResource($name)
    {
        $uri = LAABS_RESOURCE . LAABS_URI_SEPARATOR . $name;
        $path = $this->getPath($uri);

        if (!$path) {
            throw new \core\Exception("Resource '$this->name/$name' not found");
        }

        return new Resource($name, $path, $this->instance);
    }

    /**
     * Returns the class to use for a given service name, searching the extensions
     * @param string $uri The uri of the service to find on the app. Can be a simple local name or a qualified name
     * 
     * @return string The fully qualified class
     */
    public function getClassName($uri)
    {
        $class = LAABS_APP . LAABS_NS_SEPARATOR 
            . $this->name . LAABS_NS_SEPARATOR 
            . str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);

        if (class_exists($class)) {
            return $class;
        }
    }

    /**
     * Returns the interface to use for a given interface name, searching the extensions
     * @param string $uri The uri of the interface to find on the app. Can be a simple local name or a qualified name
     * 
     * @return string The full qualified extended interface
     */
    public function getInterface($uri)
    {
        $interface = LAABS_APP . LAABS_NS_SEPARATOR . 
            $this->name . LAABS_NS_SEPARATOR 
            . str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);

        if (interface_exists($interface)) {
            return $interface;
        }
    }

    /* Route API */
    /**
     * Checks if an API is defined on the bundle
     * @param string $name The name of the API
     * 
     * @return bool
     */
    public function hasAPI($name)
    {
        return ($this->getAPI($name) != false);
    }

    /**
     * Get a API definition
     * @param string $name The name of the API
     * 
     * @return object the \core\Reflection\API object
     * 
     * @throws core\Reflection\Exception if the API is unknown or can not be instantiated
     */
    public function getAPI($name)
    {
        $uri = $name . LAABS_INTERFACE;
        $interface = $this->getInterface($uri);

        if (!$interface) {
            throw new \Exception("API '$this->name/$name' not found");
        }

        $API = new API($name, $interface, $this);

        return $API;
    }

    /**
     * Get all the presenters defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\Presenter objects
     */
    public function getAPIs()
    {
        $APIs = array();

        /* Search path in base path */
        //$dir = ".." . DIRECTORY_SEPARATOR . LAABS_APP . DIRECTORY_SEPARATOR . $this->name;
        $dir = LAABS_APP . DIRECTORY_SEPARATOR . $this->name;
        foreach (scandir($dir) as $entry) {
            if (!is_file($dir . DIRECTORY_SEPARATOR . $entry) || substr($entry, -13) != LAABS_INTERFACE . ".php") {
                continue;
            }

            $name = \laabs\basename($entry, LAABS_INTERFACE . '.php');
            $interface = LAABS_APP . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . \laabs\basename($entry, '.php');
            $APIs[] = new API($name, $interface, $this);
        }

        return $APIs;
    }

    /* Presenter */
    /**
     * Checks if a presenter is defined on the bundle
     * @param string $name The name of the presenter
     * 
     * @return bool
     */
    public function hasPresenter($name)
    {
        return ($this->getPresenter($name) != false);
    }

    /**
     * Get a presenter definition
     * @param string $name The name of the presenter
     * 
     * @return object the \core\Reflection\Presenter object
     * 
     * @throws core\Reflection\Exception if the presenter is unknown or can not be instantiated
     */
    public function getPresenter($name)
    {
        $uri = LAABS_PRESENTER . LAABS_URI_SEPARATOR . $name;
        $class = $this->getClassName($uri);
        if (!$class) {
            throw new \Exception("Presenter '$this->name/$name' not found");
        }

        $presenter = new Presenter($name, $class, $this);
        if (!$presenter->isInstantiable()) {
            throw new \Exception("Presenter '$this->name/$name' is not instantiable");
        }

        return $presenter;
    }

    /**
     * Get all the presenters defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\Presenter objects
     */
    public function getPresenters()
    {
        $presenters = array();

        $presenterClasses = Extensions::extendedClasses(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_PRESENTER);
        foreach ($presenterClasses as $presenterClass) {
            $presenterName = \laabs\basename($presenterClass);
            $presenter = new Presenter($presenterName, $presenterClass, $this);
            if ($presenter->isInstantiable()) {
                $presenters[] = $presenter;
            }
        }

        return $presenters;
    }

}
