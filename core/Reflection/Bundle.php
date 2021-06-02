<?php
/**
 * Class file for Bundle definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Bundle definitions
 * 
 * @extends \core\Reflection\abstractContainer
 */
class Bundle
    extends abstractContainer
{

    /* Constants */

    /* Properties */

    /* Methods */
    /**
     * Get a bundle instance singleton.
     * The instance singleton is identified by the name of bundle
     * @param string $name The name of the bundle to get instance of
     * 
     * @return \core\Reflection\Bundle object
     */
    public static function getInstance($name)
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new Bundle($name);
        }

        return self::$instances[$name];
    }

    /**
     * Constructs a new bundle
     * @param string $name The name of the bundle
     * 
     * @return void
     * 
     * @access protected
     */
    protected function __construct($name)
    {
        $this->name = $name;

        $this->uri = $name;

        $this->instance = $name;

        if (!$extendedPath = Extensions::extendedPath(LAABS_BUNDLE . DIRECTORY_SEPARATOR . $this->name)) {
            throw new \Exception("Bundle '$name' not found");
        }

        /* Load Configuration */
        $this->configuration = \laabs::configuration($this->name);
    }

    /* Routines */
    /**
     * Return the name of bundle
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the class to use for a given service name, searching the extensions
     * @param string $uri The uri of the service to find on the bundle. Can be a simple local name or a qualified name
     * 
     * @return string The full qualified extended class
     */
    public function getClassName($uri)
    {
        $class = LAABS_BUNDLE . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);

        if ($extendedClass = Extensions::extendedClass($class)) {
            return $extendedClass;
        }
    }

    /**
     * Returns the interface to use for a given service name, searching the extensions
     * @param string $uri The uri of the service to find on the bundle. Can be a simple local name or a qualified name
     * 
     * @return string The full qualified extended interface
     */
    public function getInterface($uri)
    {
        $interface = LAABS_BUNDLE . LAABS_NS_SEPARATOR 
            . $this->name . LAABS_NS_SEPARATOR 
            . str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $uri);

        if ($extendedInterface = Extensions::extendedInterface($interface)) {
            return $extendedInterface;
        }
    }

    /**
     * Return the extended path for a given filename
     * @param string $uri The uri of resource to find on the bundle. Can be a simple local name or a qualified name
     * 
     * @return string The full qualified extended path
     */
    public function getPath($uri)
    {
        $path = LAABS_BUNDLE . DIRECTORY_SEPARATOR 
            . $this->name . DIRECTORY_SEPARATOR 
            . str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $uri);

        $extendedPath = Extensions::extendedPath($path);

        return $extendedPath;
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
        //$uri = $name . LAABS_INTERFACE;

        $uri = LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->getName() . LAABS_URI_SEPARATOR . $name . LAABS_INTERFACE;

        $API = \laabs::getCache($uri);
        if (!$API) {
            $interface = $this->getInterface($name . LAABS_INTERFACE);

            if (!$interface) {
                throw new \Exception("API '$this->name/$name' not found");
            }

            $API = new API($uri, $interface, $this);

            \laabs::setCache($uri, $API);
        }

        return $API;
    }

    /**
     * Get all the controllers defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\Controller objects
     */
    public function getAPIs()
    {
        $APIs = array();

        $interfaces = Extensions::extendedInterfaces(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name);
        foreach ($interfaces as $interface) {
            $uri = $this->name . LAABS_URI_SEPARATOR . \laabs\basename($interface);
            $API = \laabs::getCache($uri);
            if (!$API) {
                $API = new API($uri, $interface, $this);

                \laabs::setCache($uri, $API);
            }

            $APIs[] = $API;
        }

        return $APIs;
    }

    /* Controller */
    /**
     * Checks if a controller is defined on the bundle
     * @param string $name The name of the controller
     * 
     * @return bool
     */
    public function hasController($name)
    {
        return ($this->getController($name) != false);
    }

    /**
     * Get a controller definition
     * @param string $name The name of the controller
     * 
     * @return object the \core\Reflection\Controller object
     * 
     * @throws core\Reflection\Exception if the controller is unknown or can not be instantiated
     */
    public function getController($name)
    {
        $uri = LAABS_CONTROLLER . LAABS_URI_SEPARATOR . $name;

        $class = $this->getClassName($uri);

        if (!$class) {
            throw new \Exception("Controller '$this->name/$name' not found");
        }

        $controller = new Controller($name, $class, $this);

        if (!$controller->isInstantiable()) {
            throw new \Exception("Controller '$this->name/$name' is not instantiable");
        }

        return $controller;
    }

    /**
     * Get all the controllers defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\Controller objects
     */
    public function getControllers()
    {
        $controllers = array();

        $controllerClasses = Extensions::extendedClasses(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_CONTROLLER);
        foreach ($controllerClasses as $controllerClass) {
            $controllerName = \laabs\basename($controllerClass);
            $controller = new Controller($controllerName, $controllerClass, $this);
            if ($controller->isInstantiable()) {
                $controllers[] = $controller;
            }
        }

        return $controllers;
    }

    /* Parser */
    /**
     * Checks if a parser is defined on the bundle
     * @param string $name The name of the parser
     * @param string $type The type/adapter of the parser. Represents the contentType of the request
     * 
     * @return bool
     */
    public function hasParser($name, $type)
    {
        return ($this->getParser($name, $type) != false);
    }

    /**
     * Get a parser definition
     * @param string $name The name of the parser
     * @param string $type The type/adapter of the parser. Represents the contentType of the request
     * 
     * @return object the \core\Reflection\Parser object
     * @throws core\Reflection\Exception if the parser is unknown or can not be instantiated
     */
    public function getParser($name, $type)
    {
        $uri = LAABS_PARSER . LAABS_URI_SEPARATOR . $type . LAABS_URI_SEPARATOR . $name;
        $class = $this->getClassName($uri);

        if (!$class) {
            throw new \Exception("Parser '$this->name::$name' not found for content type $type");
        }

        $parser = new Parser($name, $type, $class, $this);

        if (!$parser->isInstantiable()) {
            throw new \Exception("Parser '$this->name::$name' is not instantiable");
        }

        return $parser;
    }

    /**
     * Get all the parsers defined on the bundle for a given name or type
     * @param string $name The name of the parsers to find
     * @param string $type The type/adapter of the parsers to list
     * 
     * @return array An array of all the \core\Reflection\Parser objects matching the name or type
     */
    public function getParsers($name=false, $type=false)
    {
        $parsers = array();
        $parserDirs = Extensions::extendedContents(LAABS_BUNDLE . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . LAABS_PARSER);
        foreach ($parserDirs as $parserDir) {
            $parserType = \laabs\basename($parserDir);
            if (!$type || $type == $parserType) {
                $parserTypeClasses = Extensions::extendedClasses(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_PARSER . LAABS_URI_SEPARATOR . $parserType);
                foreach ($parserTypeClasses as $parserTypeClass) {
                    $parserName = \laabs\basename($parserTypeClass);
                    if (!$name || $parserName == $name) {
                        $parser = new Parser($name, $type, $parserTypeClass, $this);
                        if ($parser->isInstantiable()) {
                            $parsers[] = $parser;
                        }
                    }
                }
            }
        }

        return $parsers;
    }


    /* Serializer */
    /**
     * Checks if a serializer is defined on the bundle
     * @param string $name The name of the serializer
     * @param string $type The type/adapter of the serializer. Represents the contentType of the response
     *
     * @return bool
     */
    public function hasSerializer($name, $type)
    {
        return ($this->getSerializer($name, $type) != false);
    }

    /**
     * Get a serializer definition
     * @param string $name The name of the serializer
     * @param string $type The type/adapter of the serializer. Represents the contentType of the response
     * 
     * @return object the \core\Reflection\Serializer object
     * 
     * @throws core\Reflection\Exception if the serializer is unknown or can not be instantiated
     */
    public function getSerializer($name, $type)
    {
        $uri = LAABS_SERIALIZER . LAABS_URI_SEPARATOR . $type . LAABS_URI_SEPARATOR . $name;
        $class = $this->getClassName($uri);

        if (!$class) {
            throw new \Exception("Serializer '$this->name::$name' not found for content type $type");
        }

        $serializer = new Serializer($name, $type, $class, $this);

        if (!$serializer->isInstantiable()) {
            throw new \Exception("Serializer '$this->name::$name' is not instantiable");
        }

        return $serializer;
    }

    /**
     * Get all the serializers defined on the bundle for a given name or type
     * @param string $name The name of the serializers to find
     * @param string $type The type/adapter of the serializers to list
     * 
     * @return array An array of all the \core\Reflection\Serializer objects matching the name or type
     */
    public function getSerializers($name=false, $type=false)
    {
        $serializers = array();
        $serializerDirs = Extensions::extendedContents(LAABS_BUNDLE . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . LAABS_SERIALIZER);
        foreach ($serializerDirs as $serializerDir) {
            $serializerType = \laabs\basename($serializerDir);
            if (!$type || $type == $serializerType) {
                $serializerTypeClasses = Extensions::extendedClasses(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_SERIALIZER . LAABS_URI_SEPARATOR . $type);
                foreach ($serializerTypeClasses as $serializerTypeClass) {
                    $serializerName = \laabs\basename($serializerTypeClass);
                    if (!$name || $serializerName == $name) {
                        $serializer = new Serializer($name, $type, $serializerTypeClass, $this);
                        if ($serializer->isInstantiable()) {
                            $serializers[] = $serializer;
                        }

                    }
                }
            }
        }

        return $serializers;
    }

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
            throw new \Exception("Resource '$this->name/$name' not found");
        }

        return new Resource($name, $path, $this->instance);
    }

    /* Model */
    /**
     * Checks if a class is defined on the bundle
     * @param string $name The name of the class
     * 
     * @return bool
     */
    public function hasClass($name)
    {
        return ($this->getClass($name) != false);
    }

    /**
     * Get a class definition
     * @param string $name The name of the class
     * 
     * @return object the \core\Reflection\Type object
     * @throws core\Reflection\Exception if the class is unknown or can not be instantiated
     */
    public function getClass($name)
    {
        $key = $this->getName() . LAABS_URI_SEPARATOR . LAABS_MODEL . LAABS_URI_SEPARATOR . $name;

        $class = \laabs::getCache($key);

        if (!$class) {

            $uri = LAABS_MODEL . LAABS_URI_SEPARATOR . $name;
            $classname = $this->getClassName($uri);
            if (!$classname) {
                throw new \Exception("Type '$this->name/$name' not found");
            }

            $class = new Type($name, $classname, $this);

            \laabs::setCache($key, $class);
        }

        return $class;
    }

    /**
     * Get all the types defined on the bundle
     * @return array An array of all the \core\Reflection\Type objects
     */
    public function getClasses()
    {
        $classes = array();
        $classnames = Extensions::extendedClasses(LAABS_BUNDLE. LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_MODEL);
        foreach ($classnames as $classname) {
            $key = $this->getName() . LAABS_URI_SEPARATOR . LAABS_MODEL . LAABS_URI_SEPARATOR . \laabs\basename($classname);

            $class = \laabs::getCache($key);
            if (!$class) {
                $class = new Type(\laabs\basename($classname), $classname, $this);
                \laabs::getCache($key, $class);
            }

            $classes[$class->getName()] = $class;
        }

        return $classes;
    }

    /* Messages */
    /**
     * Checks if a message is defined on the bundle
     * @param string $name The name of the message
     * 
     * @return bool
     */
    public function hasMessage($name)
    {
        return ($this->getMessage($name) != false);
    }

    /**
     * Get a message definition
     * @param string $name The name of the message
     * 
     * @return object the \core\Reflection\Message object
     * @throws core\Reflection\Exception if the message is unknown or can not be instantiated
     */
    public function getMessage($name)
    {
        $key = $this->getName() . LAABS_URI_SEPARATOR . LAABS_MESSAGE . LAABS_URI_SEPARATOR . $name;

        $message = \laabs::getCache($key);

        if (!$message) {

            $uri = LAABS_MESSAGE . LAABS_URI_SEPARATOR . $name;
            $messagename = $this->getClassName($uri);
            if (!$messagename) {
                throw new \Exception("Message '$this->name/$name' not found");
            }

            $message = new Message($name, $messagename, $this);

            \laabs::setCache($key, $message);
        }

        return $message;
    }

    /**
     * Get all the types defined on the bundle
     * @return array An array of all the \core\Reflection\Message objects
     */
    public function getMessages()
    {
        $messages = array();
        $messagenames = Extensions::extendedClasses(LAABS_BUNDLE. LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_MESSAGE);
        foreach ($messagenames as $messagename) {
            $key = $this->getName() . LAABS_URI_SEPARATOR . LAABS_MESSAGE . LAABS_URI_SEPARATOR . \laabs\basename($messagename);

            $message = \laabs::getCache($key);
            if (!$message) {
                $message = new Message(\laabs\basename($messagename), $messagename, $this);
                \laabs::getCache($key, $message);
            }

            $messages[$message->getName()] = $message;
        }

        return $messages;
    }
    
    /* Exception */
    /**
     * Checks if an exception is defined on the bundle
     * @param string $name The name of the exception
     * 
     * @return bool
     */
    public function hasException($name)
    {
        return (($this->getClassName(LAABS_EXCEPTION . LAABS_URI_SEPARATOR . $name)) != false);
    }

    /**
     * Get an exception definition
     * @param string $name The name of the exception
     * 
     * @return object the \core\Reflection\Exception object
     * 
     * @throws core\Reflection\Exception if the exception is unknown or can not be instantiated
     */
    public function getException($name)
    {
        $uri = LAABS_EXCEPTION . LAABS_URI_SEPARATOR . $name;
        $class = $this->getClassName($uri);
        if (!$class) {
            throw new \Exception("Exception '$this->name/$name' not found");
        }

        $exception = new Exception($name, $class, $this);

        if (!$exception->isInstantiable()) {
            throw new \Exception("Exception '$this->name/$name' is not instantiable");
        }

        return $exception;
    }

    /**
     * Get all the exceptions defined on the bundle
     * 
     * @return array An array of all the \core\Reflection\Exception objects
     */
    public function getExceptions()
    {
        $exceptions = array();
        $exceptionClasses = Extensions::extendedClasses(LAABS_BUNDLE. LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_EXCEPTION);
        foreach ($exceptionClasses as $exceptionClass) {
            $exceptions[] = new Exception(\laabs\basename($exceptionClass), $exceptionClass, $this);
        }

        return $exceptions;
    }

    /**
     * Instantiate a new exception object from bundle exception component
     * @param string $nameUri The name of the exception to instantiate
     * 
     * @return object
     */
    public function newException($nameUri)
    {
        $exception = $this->getException($nameUri);

        $args = func_get_args();
        array_shift($args);

        return $exception->newInstance($args);
    }

        /**
     * Indicates whether the container has a batch job or not
     * @param string $jobname The name of the job
     * 
     * @return bool
     */
    public function hasJob($jobname)
    {
        return ($this->getClassName(LAABS_BATCH . LAABS_URI_SEPARATOR . $jobname) !== null);
    }

    /**
     * Returns a core reflection job object
     * @param string $jobname The name of the job
     * 
     * @return \core\Reflection\Job The job object
     * 
     * @throws Exception if the job is not declared by the container
     */
    public function getJob($jobname)
    {
        $class = $this->getClassName(LAABS_BATCH . LAABS_URI_SEPARATOR . $jobname);
        if (!$class) {
            throw new \core\Exception("Undefined job '$this->uri/$jobname'");
        }

        $job = new Job($jobname, $class, $this);

        return $job;
    }

    /**
     * List the jobs
     * 
     * @return \core\Reflection\Job[] An array of jobs classes
     */
    public function getJobs()
    {
        $jobs = array();

        $jobClasses = Extensions::extendedClasses(LAABS_BUNDLE. LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_BATCH);
        foreach ($jobClasses as $jobClass) {
            $jobName = \laabs\basename($jobClass);
            $job = new Job($jobName, $jobClass, $this);
            if ($job->isInstantiable()) {
                $jobs[] = $job;
            }
        }

        return $jobs;
    }

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

        $observerClasses = Extensions::extendedClasses(LAABS_BUNDLE . LAABS_URI_SEPARATOR . $this->name . LAABS_URI_SEPARATOR . LAABS_OBSERVER, true);
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
