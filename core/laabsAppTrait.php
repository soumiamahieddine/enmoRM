<?php
/**
 * Trait for app source management
 */
trait laabsAppTrait
{
    /* Properties */
    protected static $bundles = array();

    protected static $dependencies = array();

    protected static $classes = array();
    
    protected static $controllers = array();

    protected static $observers = array();
    /* Methods */
    /**
     * Get the current app
     * @return object
     */
    public static function app()
    {
        return \core\Reflection\App::getInstance();
    }

    /**
     * Get the current presentation
     * @return object
     */
    public static function presentation()
    {
        return \core\Reflection\Presentation::getInstance();
    }

    /**
     * Check the client is a data client (web service)
     * @return bool
     */
    public static function isServiceClient()
    {
        if (
            (
            !empty($_SERVER['SERVICE_CLIENT_TOKEN']) 
            && strpos($_SERVER['HTTP_USER_AGENT'], $_SERVER['SERVICE_CLIENT_TOKEN']) !== false
            ) 
            || !static::hasPresentation()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get dependency injection
     * @param string $name   The name of the dependency instance
     * @param string $caller The uri of the caller instance
     *
     * @return object The Dependency Injection
     */
    public static function dependency($name, $caller = false)
    {
        if (!self::hasDependency($name)) {
            throw new \core\Exception("Dependency $name is not activated. Check configuration of the instance.");
        }

        if ($caller) {
            $containerRouter = new \core\Route\ContainerRouter($caller);
            $caller = $containerRouter->container;
        } else {
            $caller = null;
        }

        return \core\Reflection\Dependency::getInstance($name, $caller);
    }

    /**
     * Get dependency injections
     *
     * @return object The Dependency Injection
     */
    public static function dependencies()
    {
        if (empty(self::$dependencies)) {
            foreach (self::getDependencies() as $dependency) {
                self::$dependencies[$dependency] = \core\Reflection\Dependency::getInstance($dependency);
            }
        }

        return self::$dependencies;
    }

    /**
     * Get all the bundles
     * @return array The bundle objects
     */
    public static function bundles()
    {
        if (empty(self::$bundles)) {
            foreach (self::getBundles() as $bundle) {
                self::$bundles[$bundle] = \core\Reflection\Bundle::getInstance($bundle);
            }
        }

        return self::$bundles;
    }

    /**
     * Get a bundle
     * @param string $name The name of the bundle. If null current buendle i returned
     *
     * @return \core\Reflection\Bundle The bundle object
     */
    public static function bundle($name)
    {
        if (!self::hasBundle($name)) {
            throw new \core\Exception("Bundle $name is not activated. Check configuration of the instance.");
        }

        if (empty(self::$bundles)) {
            self::bundles();
        }

        return self::$bundles[$name];
    }

    /**
     * Get the available ui commands
     *
     * @return array
     */
    public static function commands()
    {
        $commands = \laabs::getCache('commands');

        if (!$commands) {
            $commands = array();
            $presentation = self::presentation();
            $userStories = $presentation->getUserStories();

            \laabs::notify(LAABS_USER_STORY, $userStories);

            foreach ($userStories as $userStory) {
                foreach ($userStory->getUserCommands() as $userCommand) {
                    $commands[] = $userCommand;
                }
            }

            \laabs::setCache('commands', $commands);
        }

        return $commands;
    }

    /**
     * Get the available us
     *
     * @return array
     */
    public static function userStories()
    {
        $userStories = \laabs::getCache('userStories');

        if (!$userStories) {
            $userStories = array();
            $presentation = self::presentation();
            $userStories = $presentation->getUserStories();

            \laabs::notify(LAABS_USER_STORY, $userStories);

            \laabs::setCache('userStories', $userStories);
        }

        return $userStories;
    }

    /**
     * Get the service associated with the given command uri
     * @param string $method The CRUD method
     * @param string $uri    The uri received
     *
     * @return object The command object
     */
    public static function command($method, $uri)
    {
        $method = strtolower($method);

        // Try all commands
        foreach (self::userStories() as $userStory) {
            foreach ($userStory->getUserCommands() as $userCommand) {
                if ($matchedUserCommand = $userCommand->match($method, $uri)) {
                    return $matchedUserCommand;
                }
            }
        }

        throw new \core\Exception\NotFoundException('Undefined user command for route %1$s %2$s', 404, null, [$method, $uri]);
    }

    /**
     * Get the action associated with the given action uri
     * @param string $method The CRUD method
     * @param string $uri    The uri received
     *
     * @return object The path object
     */
    public static function route($method, $uri)
    {
        $method = strtolower($method);
        $steps = explode(LAABS_URI_SEPARATOR, $uri);

        $bundle = array_shift($steps);
        $reflectionBundle = self::bundle($bundle);

        // Search qualified API
        $api = array_shift($steps);
        $reflectionApi = $reflectionBundle->getApi($api);
        if (!$reflectionApi) {
            throw new \core\Exception("Undefined api $api for route $method $uri");
        }

        // Get uri from remaining path steps
        $path = implode(LAABS_URI_SEPARATOR, $steps);

        $reflectionPaths = $reflectionApi->getPaths();
        foreach ($reflectionPaths as $reflectionPath) {
            if ($matchedPath = $reflectionPath->match($method, $path)) {
                return $matchedPath;
            }
        }

        throw new \core\Exception("Undefined route $method $uri");
    }

    /**
     * Call a service
     * @param string $name
     * 
     * @return the result of action
     */
    public static function callService($name)
    {
        $serviceArgs = func_get_args();
        array_shift($serviceArgs);       
        
        $pathRouter = new \core\Route\PathRouter($name);

        $actionRouter = new \core\Route\ActionRouter($pathRouter->path->action);
        
        $controller = $actionRouter->controller->newInstance();

        $actionReturn = $actionRouter->action->callArgs($controller, $serviceArgs);

        return $actionReturn;
    }

    /**
     * Call a service with array parameters
     * @param string $name
     * 
     * @return the result of action
     */
    public static function callServiceArgs($name,$serviceArgs)
    {
        $pathRouter = new \core\Route\PathRouter($name);

        $actionRouter = new \core\Route\ActionRouter($pathRouter->path->action);
        
        $controller = $actionRouter->controller->newInstance();

        $actionReturn = $actionRouter->action->callArgs($controller, $serviceArgs);

        return $actionReturn;
    }
    
    /**
     * Call an input by route   
     * @param string $inputRequest The uri of input (METHOD + route or default bundle/controller/action). All additional parameters will be considered as action arguments
     * @param string $type         The type of input (html, json...)
     * @param mixed  $data         The data to parse
     * 
     * @return mixed
     */
    public static function callInput($inputRequest, $type, $data)
    {
        $method = strtok($inputRequest, ' ');
        $path = strtok(' ');
    
        $route = self::route($method, $path);

        $inputRouter = new \core\Route\InputRouter($route->action, $type);

        $parser = $inputRouter->parser->newInstance();

        $input = $inputRouter->input->parse($parser, $data);

        return $input;
    }

    /**
     * Call an output by route   
     * @param string $outputRequest The uri of output (METHOD + route or default budnle/controller/action). All additional parameters will be considered as action arguments
     * @param string $type          The type of output (html, json...)
     * @param mixed  $data          The data to serializes
     * 
     * @return mixed
     */
    public static function callOutput($outputRequest, $type, $data)
    {
        $method = strtok($outputRequest, ' ');
        $path = strtok(' ');
    
        $route = self::route($method, $path);

        $outputRouter = new \core\Route\OutputRouter($route->action, $type);

        $serializer = $outputRouter->serializer->newInstance();

        $output = $outputRouter->output->serialize($serializer, $data);

        return $output;
    }

    /**
     * Call a controller action   
     * @param string $actionUri The uri of action (METHOD + route or default budnle/controller/action). All additional parameters will be considered as action arguments
     * 
     * @return mixed
     */
    public static function callAction($actionUri)
    {
        $passedArgs = func_get_args();
        array_shift($passedArgs);

        $steps = \laabs\explode(LAABS_URI_SEPARATOR, $actionUri);
        $actionRouter = new \core\Route\ActionRouter(implode(LAABS_URI_SEPARATOR, array_splice($steps, 0, 3)));
        $actionArgs = $passedArgs;

        $controller = $actionRouter->controller->newInstance();
 
        return $actionRouter->action->call($controller, $actionArgs);
    }

    /**
     * Call a presetner view   
     * @param string $viewName The uri of view
     * @param array  $data     The data to present
     * 
     * @return mixed
     */
    public static function presentView($viewName, array $data=array())
    {
        $viewRouter = new \core\Route\viewRouter($viewName);

        $presenter = $viewRouter->presenter->newInstance();
 
        return $viewRouter->view->present($presenter, $data);
    }

    /**
     * Get the available types
     *
     * @return array
     */
    public static function classes()
    {
        if (empty(self::$classes)) {
            foreach (self::bundles() as $bundle) {
                foreach ($bundle->getClasses() as $class) {
                    self::$classes[] = $class;
                }
            }
        }
        
        return self::$classes;
    }
    
    /**
     * Get the available types
     *
     * @return array
     */
    public static function controllers()
    {
        if (empty(self::$controllers)) {
            foreach (self::bundles() as $bundle) {
                foreach ($bundle->getControllers() as $controller) {
                    self::$controllers[] = $controller;
                }
            }
        }
        
        return self::$controllers;
    }

    /**
     * Get the available observers
     *
     * @return array
     */
    public static function observers()
    {
        if (empty(self::$observers)) {
            if ($presentation = self::presentation()) {
                foreach ($presentation->getObservers() as $observer) {
                    self::$observers[] = $observer;
                }
            }

            foreach (self::bundles() as $bundle) {
                foreach ($bundle->getObservers() as $observer) {
                    self::$observers[] = $observer;
                }
            }
        }

        return self::$observers;
    }

    /**
     * Attach an observer
     * @param string $methoduri
     * @param string $subject
     */
    public static function attachObserver($methoduri, $subject)
    {
        $methodRouter = new \core\Route\MethodRouter($methoduri);
        $observer = $methodRouter->service;
        $handler = $methodRouter->method;
        
        $observerObject = $observer->newInstance();
            
        \core\Observer\Dispatcher::attach(
            $observerObject,
            $handler->name,
            $subject
        );
    }


    /**
     * Instanciate a new controller. All additional arguments will be passed to constructor
     * @param string $controllerName The controller name bundle/controller
     *
     * @return object
     */
    public static function newController($controllerName)
    {
        $constructorArgs = func_get_args();
        $argsHash = md5(serialize($constructorArgs));
        
        if (!isset($GLOBALS[$argsHash])) {
            $bundleName = strtok($controllerName, LAABS_URI_SEPARATOR);
            $controllerName = strtok(LAABS_URI_SEPARATOR);      
            array_shift($constructorArgs);

            $controller = self::bundle($bundleName)->getController($controllerName);

            $GLOBALS[$argsHash] = $controller->newInstance($constructorArgs);

        } 

        return $GLOBALS[$argsHash];
    }

    /**
     * Instanciate a new presenter. All additional arguments will be passed to constructor
     * @param string $name The presenter name domain/presenter
     *
     * @return object
     */
    public static function newPresenter($name)
    {
        $constructorArgs = func_get_args();
        array_shift($constructorArgs);
        
        $presenter = \laabs::presentation()->getPresenter($name)->newInstance($constructorArgs);

        return $presenter;
    }

    /**
     * Instanciate a new parser. All additional arguments will be passed to constructor
     * @param string $name The parser name bundle/parser
     * @param string $type The type of data to parse
     *
     * @return object
     */
    public static function newParser($name, $type)
    {
        $bundleName = strtok($name, LAABS_URI_SEPARATOR);
        $parserName = strtok(LAABS_URI_SEPARATOR);

        $constructorArgs = func_get_args();
        array_shift($constructorArgs);
        array_shift($constructorArgs);

        $parser = self::bundle($bundleName)->getParser($parserName, $type);

        return $parser->newInstance($constructorArgs);
    }

    /**
     * Instanciate a new serializer. All additional arguments will be passed to constructor
     * @param string $name The serializer name bundle/serializer
     * @param string $type The type of data to serialize
     *
     * @return object
     */
    public static function newSerializer($name, $type)
    {
        $bundleName = strtok($name, LAABS_URI_SEPARATOR);
        $serializerName = strtok(LAABS_URI_SEPARATOR);

        $constructorArgs = func_get_args();
        array_shift($constructorArgs);
        array_shift($constructorArgs);

        $serializer = self::bundle($bundleName)->getSerializer($serializerName, $type);

        return $serializer->newInstance($constructorArgs);
    }

    /**
     * Instanciate a new exception. All additional arguments will be passed to constructor
     * @param string $exceptionUri The exception name bundle/exception
     *
     * @return Exception
     */
    public static function newException($exceptionUri)
    {
        $bundleName = strtok($exceptionUri, LAABS_URI_SEPARATOR);
        $exceptionName = strtok(LAABS_URI_SEPARATOR);

        $constructorArgs = func_get_args();
        array_shift($constructorArgs);

        $exception = self::bundle($bundleName)->getException($exceptionName);

        return $exception->newInstance($constructorArgs);
    }

    /**
     * Call a service
     * @param string $serviceName The service name bundle|dependency / service|class
     *
     * @return object
     */
    public static function newService($serviceName)
    {
        $serviceRouter = new \core\Route\ServiceRouter($serviceName);

        $constructorArgs = func_get_args();
        array_shift($constructorArgs);

        return $serviceRouter->service->newInstance($constructorArgs);
    }

    /**
     * Send an event to observers
     * @param string $subject The subject for event
     * @param mixed  &$object The observed object
     * @param array  &$info   The associated data
     *
     * @return array An associative array for class::method => return of notified observers
     */
    public static function notify($subject, &$object, array &$info=null)
    {
        return \core\Observer\Dispatcher::notify($subject, $object, $info);
    }

    /**
     * Checks if a type name is a valid service name
     * @param string $type The name of the type
     *
     * @return bool
     */
    public static function isServiceType($type)
    {
        if (self::isScalarType($type)
            || $type == 'resource'
            || $type == 'NULL'
            || substr($type, -2) == "[]"
        ) {
            return false;
        }

        if ($type[0] == LAABS_NS_SEPARATOR) {
            $type = substr($type, 1);
        }

        $root = strtok($type, LAABS_NS_SEPARATOR);
        switch($root) {
            case LAABS_CORE:
            case LAABS_DEPENDENCY:
            case LAABS_BUNDLE:
            case LAABS_EXTENSION:
                return true;

            default:
                return false;
        }
    }

    /**
     * Get configuration section
     * @param string $section
     * 
     * @return mixed
     */
    public static function configuration($section=false)
    {
        $conf = \core\Configuration\Configuration::getInstance();

        if ($section) {
            $section = str_replace(LAABS_URI_SEPARATOR, ".", $section);

            if (isset($conf[$section])) {
                return $conf[$section];
            }
        } else {
            return $conf;
        }
    }

    /* Boolean value retrieval
        <<< name
        >>> bool
    */
    /**
     * Checks if the app instance storage is disabled in server configuration
     *
     * @return bool
     */
    public static function instanceDisable()
    {
        return isset($_SERVER['LAABS_INSTANCE_DISABLE']);
    }

    /**
     * Checks if the php session is disabled in server configuration
     *
     * @return bool
     */
    public static function sessionDisable()
    {
        return isset($_SERVER['LAABS_SESSION_DISABLE']);
    }

    /**
     * Checks if the output buffer should be cleaned up before the kernel sends the output
     *
     * @return bool
     */
    public static function getBufferMode()
    {
        if (isset($_SERVER['LAABS_BUFFER_MODE'])) {
            $_SERVER['LAABS_BUFFER_MODE'];
        }

        return LAABS_BUFFER_NONE;
    }

    /* Simple value retrieval
        <<< value
        >>> value
    */
    /**
     * Get the name of the app
     *
     * @return string
     * @throws \core\Exception if no name of app provided on the configuration
     */
    public static function getApp()
    {
        if (isset($_SERVER['LAABS_APP'])) {
            return $_SERVER['LAABS_APP'];
        }

        throw new \core\Exception("Invalid host configuration: no application name provided");
    }

    /**
     * Get the name of the host
     *
     * @return string
     */
    public static function getInstanceName()
    {
        if (isset($_SERVER['LAABS_INSTANCE_NAME'])) {
            if (preg_match('#[A-Za-z][A-Za-z0-9]*#', $_SERVER['LAABS_INSTANCE_NAME'])) {
                return $_SERVER['LAABS_INSTANCE_NAME'];
            }           
        }

        throw new \core\Exception("Invalid host configuration: no instance name provided");
    }

    /**
     * Get the name of the presentation layer
     *
     * @return string
     * @throws \core\Exception if no name of presentation provided on the configuration
     */
    public static function getPresentation()
    {
        if (isset($_SERVER['LAABS_PRESENTATION'])) {
            return $_SERVER['LAABS_PRESENTATION'];
        }
    }

    /**
     * Get the http method used (GET | POST | PUT | DELETE)
     *
     * @return string
     */
    public static function getHttpMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return $_SERVER['REQUEST_METHOD'];
        }

        return 'GET';
    }

    /**
     * Get the additional php configuration file for runtime directives
     *
     * @return string
     */
    public static function getPhpConfiguration()
    {
        if (isset($_SERVER['LAABS_PHP_INI'])) {
            return $_SERVER['LAABS_PHP_INI'];
        }
    }

    /**
     * Get the configuration file name
     *
     * @return string
     */
    public static function getConfiguration()
    {
        if (isset($_SERVER['LAABS_CONFIGURATION'])) {
            return $_SERVER['LAABS_CONFIGURATION'];
        }

        return ".." . DIRECTORY_SEPARATOR 
            . LAABS_DATA . DIRECTORY_SEPARATOR 
            . self::getApp() . DIRECTORY_SEPARATOR 
            . 'conf' . DIRECTORY_SEPARATOR . 'configuration.ini';
    }

    /**
     * Checks if a bundle is enabled or not
     * @param string $bundle The name of bundle to check
     *
     * @return bool
     */
    public static function hasBundle($bundle)
    {
        if (in_array($bundle, self::getBundles())) {
            return true;
        }
    }

    /**
     * Checks if a dependency is enabled or not
     * @param string $dependency The name of dependency to check
     *
     * @return bool
     */
    public static function hasDependency($dependency)
    {
        if (in_array($dependency, self::getDependencies())) {
            return true;
        }
    }

    /**
     * Checks if a presentation layer is enabled or not
     *
     * @return bool
     */
    public static function hasPresentation()
    {
        if (self::getPresentation()) {
            return true;
        }
    }

    /**
     * Get the Http cache control default value
     *
     * @return string
     */
    public static function getCacheControl()
    {
        if (isset($_SERVER['LAABS_CACHE_CONTROL'])) {
            return $_SERVER['LAABS_CACHE_CONTROL'];
        }
    }

    /**
     * Get the last request time
     * @param bool $asFloat Get the time as float or int
     *
     * @return mixed The request time
     */
    public static function getRequestTime($asFloat=false)
    {
        if ($asFloat) {
            return $_SERVER['REQUEST_TIME_FLOAT'];
        } else {
            return $_SERVER['REQUEST_TIME'];
        }
    }

    /**
     * Get the app instance storage path
     * 
     * @return string
     */
    public static function getInstanceSavePath()
    {
        if (isset($_SERVER['LAABS_INSTANCE_SAVE_PATH'])) {
            return $_SERVER['LAABS_INSTANCE_SAVE_PATH'];
        }

        if ($sessionSavePath = \session_save_path()) {
            return $sessionSavePath;
        }

        if ($tmpDir = \sys_get_temp_dir()) {
            return $tmpDir;
        }
    }

    /**
     * Get the app instance handler class
     * 
     * @return string
     */
    public static function getInstanceHandler()
    {
        if (isset($_SERVER['LAABS_INSTANCE_HANDLER'])) {
            return $_SERVER['LAABS_INSTANCE_HANDLER'];
        }

        return "\core\Repository\php";
    }
    
    /**
     * Get the buffer control callback function
     * 
     * @return string
     */
    public static function getBufferCallback()
    {
        if (isset($_SERVER['LAABS_BUFFER_CALLBACK'])) {
            return $_SERVER['LAABS_BUFFER_CALLBACK'];
        }
        
        //return "ob_gzhandler";
    }

    /**
     * Get the log path
     * 
     * @return string
     */
    public static function getLog()
    {
        if (isset($_SERVER['LAABS_LOG'])) {
            return $_SERVER['LAABS_LOG'];
        }

        if ($phplog = \ini_get('error_log')) {
            if ($phplog != 'syslog') {
                return dirname($phplog) . DIRECTORY_SEPARATOR . 'laabs_log';
            }
        }

        return 'syslog';
    }

    /**
     * Get the date format
     * 
     * @return string
     */
    public static function getDateFormat()
    {
        if (isset($_SERVER['LAABS_DATE_FORMAT'])) {
            return $_SERVER['LAABS_DATE_FORMAT'];
        }

        return 'Y-m-d';
    }

    /**
     * Get the timestamp format
     * 
     * @return string
     */
    public static function getTimestampFormat()
    {
        if (isset($_SERVER['LAABS_TIMESTAMP_FORMAT'])) {
            return $_SERVER['LAABS_TIMESTAMP_FORMAT'];
        }

        return 'Y-m-d\TH:i:s.u\Z';
    }

    /**
     * Get the decimal positions for number format
     * 
     * @return integer
     */
    public static function getNumberDecimals()
    {
        if (isset($_SERVER['LAABS_NUMBER_DECIMALS'])) {
            return (integer) $_SERVER['LAABS_NUMBER_DECIMALS'];
        }

        return false;
    }

    /**
     * Get the decimal separator for number format
     * 
     * @return string
     */
    public static function getNumberDecimalSeparator()
    {
        if (isset($_SERVER['LAABS_NUMBER_DECIMAL_SEPARATOR'])) {
            return (string) $_SERVER['LAABS_NUMBER_DECIMAL_SEPARATOR'];
        }

        return ".";
    }

    /**
     * Get the thousand separator for number format
     * 
     * @return string
     */
    public static function getNumberThousandsSeparator()
    {
        if (isset($_SERVER['LAABS_NUMBER_THOUSANDS_SEPARATOR'])) {
            return (string) $_SERVER['LAABS_NUMBER_THOUSANDS_SEPARATOR'];
        }

        return "";
    }

    /**
     * Get the XML encoding for xml type
     * 
     * @return string
     */
    public static function getXmlEncoding()
    {
        if (isset($_SERVER['LAABS_XML_ENCODING'])) {
            return (string) $_SERVER['LAABS_XML_ENCODING'];
        }

        return "utf-8";
    }

    /**
     * Get the empty route path for READ /
     * 
     * @return string
     */
    public static function getDefaultUri()
    {
        if (isset($_SERVER['LAABS_DEFAULT_URI'])) {
            return (string) $_SERVER['LAABS_DEFAULT_URI'];
        }
    }

    /**
     * Get the callback for autoload
     * 
     * @return string
     */
    public static function getAutoload()
    {
        if (isset($_SERVER['LAABS_AUTOLOAD'])) {
            return (string) $_SERVER['LAABS_AUTOLOAD'];
        } else {
            return 'laabs::autoload';
        }
    }

    /**
     * Get the callback for errors
     * 
     * @return string
     */
    public static function getExceptionHandler()
    {
        if (isset($_SERVER['LAABS_EXCEPTION_HANDLER'])) {
            if (empty($_SERVER['LAABS_EXCEPTION_HANDLER'])) {
                return 'laabs::exceptionHandler';
            } else {
                return (string) $_SERVER['LAABS_EXCEPTION_HANDLER'];
            }
        }
    }

    /**
     * Get the callback for errors
     * 
     * @return string
     */
    public static function getErrorHandler()
    {
        if (isset($_SERVER['LAABS_ERROR_HANDLER'])) {
            if (empty($_SERVER['LAABS_ERROR_HANDLER'])) {
                return 'laabs::errorHandler';
            } else {
                return (string) $_SERVER['LAABS_ERROR_HANDLER'];
            } 
        }
    }

    /**
     * Get the route for uncaught exceptions and errors
     * 
     * @return string
     */
    public static function getErrorUri()
    {
        if (isset($_SERVER['LAABS_ERROR_URI'])) {
            return (string) $_SERVER['LAABS_ERROR_URI'];
        }
    }

    /**
     * Get the cache server and port
     * 
     * @return string
     */
    public static function getMemCacheServer()
    {
        if (isset($_SERVER['LAABS_MEMCACHE_SERVER'])) {
            return (string) $_SERVER['LAABS_MEMCACHE_SERVER'];
        }
    }

    /**
     * Get the cache delay
     * 
     * @return int
     */
    public static function getMemCacheExpire()
    {
        if (isset($_SERVER['LAABS_MEMCACHE_EXPIRE'])) {
            return (int) $_SERVER['LAABS_MEMCACHE_EXPIRE'];
        }

        return 0;
    }

    /**
     * Get the tmp dir
     * 
     * @return string
     */
    public static function getTmpDir()
    {
        if (isset($_SERVER['LAABS_TMP_DIR'])) {
            $tmpDir = $_SERVER['LAABS_TMP_DIR'];
        } else {
            $tmpDir = \sys_get_temp_dir();
        }

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 755, true);
        }

        return $tmpDir;
    }

    /**
     * Get the crypt key
     *
     * @return string The crypt key
     */
    public static function getCryptKey()
    {
        if (isset($_SERVER['LAABS_CRYPT_KEY'])) {
            return (string) $_SERVER['LAABS_CRYPT_KEY'];
        } 

        return "LAABS";
    }

    /**
     * Get the crypt cipher
     *
     * @return string An openssl crypt cipher
     */
    public static function getCryptCipher()
    {
        if (isset($_SERVER['LAABS_CRYPT_CIPHER'])) {
            $cipherName = (string) $_SERVER['LAABS_CRYPT_CIPHER'];

            if (in_array($cipherName, openssl_get_cipher_methods())) {
                return $cipherName;
            }
        } 

        return "CAST5-CBC";
    }

    /* Array retrieval
        Get an indexed array of strings from separated string
        <<< item1;item2; ... itemN
        >>> array(
            0 => 'item1',
            1 => 'item2',
            ...
            M => 'itemN'
            )
    */
    /**
     * Utility to retrieve a simple list of separated value
     * @param string $key The name of the server value
     * 
     * @return array
     */
    protected static function getList($key)
    {
        if (!isset(self::$_SERVER[$key])) {
            self::$_SERVER[$key] = array();
            if (isset($_SERVER[$key])) {
                self::$_SERVER[$key] = \laabs\explode(LAABS_CONF_LIST_SEPARATOR, $_SERVER[$key]);
            }
        }

        return self::$_SERVER[$key];
    }

    /**
     * Get the list of bundles
     * 
     * @return array
     */
    public static function getBundles()
    {
        return self::getList('LAABS_BUNDLES');
    }

    /**
     * Get the list of Extensions
     * 
     * @return array
     */
    public static function getExtensions()
    {
        return self::getList('LAABS_EXTENSIONS');
    }

    /**
     * Get the list of Dependencies
     * 
     * @return array
     */
    public static function getDependencies()
    {
        return self::getList('LAABS_DEPENDENCIES');
    }

    /* Assoc retrieval
        Get an associative array from a separated string
        <<< key1:name1;key2:name2...keyN:nameN
        >>> array(
            key1 => name1,
            key2 => name2,
            ...
            keyN => nameN
            );
    */
    
    /**
     * Utility to retrieve an associative array from separated value
     * @param string $name The name of the server value
     * 
     * @return array
     */
    protected static function getAssoc($name)
    {
        if (!isset(self::$_SERVER[$name])) {
            self::$_SERVER[$name] = array();
            if (isset($_SERVER[$name])) {
                foreach (\laabs\explode(LAABS_CONF_LIST_SEPARATOR, $_SERVER[$name]) as $item) {
                    $key = strtok($item, ":");
                    $value = substr($item, strlen($key . ":"));
                    self::$_SERVER[$name][$key] = $value;
                }
            }
        }

        return self::$_SERVER[$name];
    }

    /**
     * Get the list of default parsers by content type
     * 
     * @return array
     */
    public static function getParsers()
    {
        return self::getAssoc('LAABS_PARSERS');
    }

    /**
     * Get the list of default serializers by content type
     * 
     * @return array
     */
    public static function getSerializers()
    {
        return self::getAssoc('LAABS_SERIALIZERS');
    }

    
    /**
     * Get the list of xml namespaces associated with Laabs NS
     * 
     * @return array
     */
    public static function getXmlNamespaces()
    {
        return self::getAssoc('LAABS_XML_NS');
    }

    /**
     * Get the laabs NS associated with xml ns uri
     * @param strinx $xmlNs
     * 
     * @return string Laabs NS
     */
    public static function resolveXmlNamespace($xmlNs)
    {
        if (strpos($xmlNs, 'maarch.org:laabs:') !== false) {
            return substr($xmlNs, strlen('maarch.org:laabs:'));
        }

        return array_search($xmlNs, self::getXmlNamespaces());
    }

    /**
     * Get the xml NS associated with laabs ns
     * @param strinx $ns
     * 
     * @return string Xml NS uri
     */
    public static function getXmlNamespace($ns)
    {
        if (isset(self::getXmlNamespaces()[$ns])) {
            return self::getXmlNamespaces()[$ns];
        }
    }

    /* Revert Assoc retrieval
        Get an associative array from a separated string where each item becomes an array pair
        <<< name1:key11,key12...key1n;name2:key21,key22...key2m ... nameN:keyN1,keyN2...keyNm
        >>> array(
            key11 => name1,
            key12 => name1,
            ...
            key1m => name1,
            key21 => name2,
            key22 => name2,
            ...
            key2m => name2,
            ...
            keyN1 => nameN,
            keyN2 => nameN,
            ...
            keyNm => nameN,
            );
    */
    /**
     * Utility to retrieve an associative array from separated value, reverting the key/value pairs
     * @param string $key The name of the server value
     * 
     * @return array
     */
    protected static function getRevertAssoc($key)
    {
        if (!isset(self::$_SERVER[$key])) {
            self::$_SERVER[$key] = array();
            if (isset($_SERVER[$key])) {
                foreach (\laabs\explode(LAABS_CONF_LIST_SEPARATOR, $_SERVER[$key]) as $item) {
                    $name = strtok($item, ":");
                    $values = strtok(":");
                    foreach (\laabs\explode(",", $values) as $value) {
                        self::$_SERVER[$key][$value] = $name;
                    }
                }
            }
        }

        return self::$_SERVER[$key];
    }

    /**
     * Get the list of observers/subjects to attach on a pool
     * 
     * @return array
     */
    public static function getObservers()
    {
        return self::getRevertAssoc('LAABS_OBSERVERS');
    }

    /**
     * Get the list of mimetypes/contenttypes to identify parser and serializer adapters
     * 
     * @return array
     */
    public static function getContentTypes()
    {
        return self::getRevertAssoc('LAABS_CONTENT_TYPES');
    }

    /**
     * Get the list of locales/language codes to identify languages
     * 
     * @return array
     */
    public static function getContentLanguages()
    {
        return self::getRevertAssoc('LAABS_CONTENT_LANGUAGES');
    }

    /**
     * Parse a laabs query string into query object
     * @param string $queryString
     * 
     * @return object
     */
    public static function parseQueryString($queryString)
    {
        $parser = new \core\Query\Parser();

        return $parser->parseQuery($queryString);
    }

    /**
     * The app version
     *
     * @return array
     */
    public static function getVersion()
    {
        $versions = [];
        $version = new \stdClass();
        $version->name = 'Maarch RM';
        $version->number = trim(file_get_contents('../VERSION.md'));
        $versions[] = $version;

        $extensions = \laabs::getExtensions();
        foreach ($extensions as $extension) {
            $versionPath = 'ext' . DIRECTORY_SEPARATOR . $extension . DIRECTORY_SEPARATOR . 'VERSION.md';
            if (file_exists($versionPath)) {
                $version = new \stdClass();
                $version->name = ucfirst($extension);
                $version->number = trim(file_get_contents($versionPath));
                $versions[] = $version;
            }
        }

        return $versions;
    }

    /**
     * The app Licence
     *
     * @return String
     */
    public static function getLicence()
    {
        return file_get_contents('../LICENCE.txt');

    }

}
