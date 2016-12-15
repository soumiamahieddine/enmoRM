<?php
/**
 * Class file for Presentation definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Presentation definition
 * 
 */
class Presentation
    extends abstractContainer
{

    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Get a Presentation instance singleton.
     * 
     * @return \core\Reflection\Presentation object
     */
    public static function getInstance()
    {
        if (!isset(self::$instances[0])) {
            self::$instances[0] = new Presentation();
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
        $this->name = \laabs::getPresentation();

        $this->uri = LAABS_PRESENTATION . LAABS_URI_SEPARATOR . $this->name;

        $this->instance = LAABS_PRESENTATION . LAABS_URI_SEPARATOR . $this->name;

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
        $path = LAABS_PRESENTATION . DIRECTORY_SEPARATOR 
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
        $class = LAABS_PRESENTATION . LAABS_NS_SEPARATOR 
            . $this->name . LAABS_NS_SEPARATOR 
            . str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);

        if ($extendedClass = Extensions::extendedClass($class)) {
            return $extendedClass;
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
        $interface = LAABS_PRESENTATION . LAABS_NS_SEPARATOR 
            . $this->name . LAABS_NS_SEPARATOR
            . LAABS_USER_STORY . LAABS_NS_SEPARATOR
            . str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);

        if ($extendedInterface = Extensions::extendedInterface($interface)) {
            return $extendedInterface;
        }
    }

    /* Route userStories */
    /**
     * Checks if a user story is defined on the bundle
     * @param string $name The name of the user story
     * 
     * @return bool
     */
    public function hasUserStory($name)
    {
        return ($this->getInterface($name . LAABS_INTERFACE) != false);
    }

    /**
     * Get a user story definition
     * @param string $name The name of the user story
     * 
     * @return object the \core\Reflection\userStory object
     * 
     * @throws core\Reflection\Exception if the userStory is unknown or can not be instantiated
     */
    public function getUserStory($name)
    {
        $interface = $this->getInterface($name . LAABS_INTERFACE);

        if (!$interface) {
            throw new \Exception("userStory '$this->name/$name' not found");
        }

        $userStory = new UserStory($name, $interface, $this);

        return $userStory;
    }

    /**
     * Get all the user stories defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\userStory objects
     */
    public function getUserStories()
    {
        $userStories = array();

        /* Search path in base path */
        //$dir = ".." . DIRECTORY_SEPARATOR . LAABS_PRESENTATION . DIRECTORY_SEPARATOR . $this->name;
        $dir = LAABS_PRESENTATION . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . LAABS_USER_STORY;
        /* Search class in extensions */
        $filenames = Extensions::extendedContents($dir, $unique = false);
        foreach ($filenames as $filename) {
            if (is_file($filename)) {
                if (substr($filename, -strlen(LAABS_INTERFACE . ".php")) != LAABS_INTERFACE . ".php") {
                    continue;
                }

                $name = \laabs\basename($filename, LAABS_INTERFACE . '.php');
                $interface = LAABS_PRESENTATION . LAABS_NS_SEPARATOR 
                    . $this->name . LAABS_NS_SEPARATOR 
                    . LAABS_USER_STORY . LAABS_NS_SEPARATOR
                    . \laabs\basename($filename, '.php');

                $userStories[$name] = new UserStory($name, $interface, $this);
                
            } elseif (is_dir($filename) && $filename != '.' && $filename != '..') {
                $subfilenames = scandir($filename);
                foreach ($subfilenames as $subfilename) {
                    
                    if (substr($subfilename, -strlen(LAABS_INTERFACE . ".php")) != LAABS_INTERFACE . ".php") {
                        continue;
                    }
                    $name = \laabs\basename($filename) . LAABS_URI_SEPARATOR . \laabs\basename($subfilename, LAABS_INTERFACE . '.php');
                    $interface = str_replace(DIRECTORY_SEPARATOR, LAABS_NS_SEPARATOR, $filename). LAABS_NS_SEPARATOR . \laabs\basename($subfilename, '.php');
                    if (!isset($userStories[$name])) {
                        $userStories[$name] = new UserStory($name, $interface, $this);
                    }
                }
            }
        }
        
        return $userStories;
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

    /* Composers */
    /**
     * Checks if a composer is defined on the bundle
     * @param string $name The name of the composer
     * 
     * @return bool
     */
    public function hasComposer($name)
    {
        return ($this->getComposer($name) != false);
    }

    /**
     * Get a composer definition
     * @param string $name The name of the composer
     * 
     * @return object the \core\Reflection\Composer object
     * 
     * @throws core\Reflection\Exception if the composer is unknown or can not be instantiated
     */
    public function getComposer($name)
    {
        $uri = LAABS_COMPOSER . LAABS_URI_SEPARATOR . $name;

        $class = $this->getClassName($uri);
        
        if (!$class) {
            throw new \Exception("Composer '$this->name/$name' not found");
        }

        $composer = new Composer($name, $class, $this);
        
        if (!$composer->isInstantiable()) {
            throw new \Exception("Composer '$this->name/$name' is not instantiable");
        }

        return $composer;
    }

    /**
     * Get all the composers defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\Composer objects
     */
    public function getComposers()
    {
        $composers = array();

        $composerClasses = Extensions::extendedClasses(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_COMPOSER);
        foreach ($composerClasses as $composerClass) {
            $composerName = \laabs\basename($composerClass);
            $composer = new Composer($composerName, $composerClass, $this);
            if ($composer->isInstantiable()) {
                $composers[] = $composer;
            }
        }

        return $composers;
    }

    /* Observers */
    /**
     * Indicates whether the container has an observer or not
     * @param string $observerName The name of the observer
     * 
     * @return bool
     */
    public function hasObserver($observerName)
    {
        return ($this->getClassName(LAABS_OBSERVER . LAABS_URI_SEPARATOR . $observerName) !== null);
    }

    /**
     * Returns a core reflection observer object
     * @param string $observerName The name of the observer
     * 
     * @return \core\Reflection\Observer The observer object
     * 
     * @throws Exception if the observer is not declared by the container
     */
    public function getObserver($observerName)
    {
        $class = $this->getClassName(LAABS_OBSERVER . LAABS_URI_SEPARATOR . $observerName);
        if (!$class) {
            throw new \core\Exception("Undefined observer '$this->uri/$service'");
        }

        $observer = new Observer($observerName, $class, $this);

        return $observer;
    }

    /**
     * List the observers
     * 
     * @return \core\Reflection\Observer[] An array of observers classes
     */
    public function getObservers()
    {
        $observers = array();

        $observerClasses = Extensions::extendedClasses(
            LAABS_PRESENTATION . LAABS_URI_SEPARATOR 
            . $this->name . LAABS_URI_SEPARATOR 
            . LAABS_OBSERVER
        );
        foreach ($observerClasses as $observerClass) {
            $observerName = \laabs\basename($observerClass);
            $observer = new Observer($observerName, $observerClass, $this);
            if ($observer->isInstantiable()) {
                $observers[] = $observer;
            }
        }

        return $observers;
    }
}
