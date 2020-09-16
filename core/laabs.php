<?php
require_once __DIR__.DIRECTORY_SEPARATOR.'constants.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'functions.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'laabsAppTrait.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'laabsModelTrait.php';
/**
 * The main Laabs API class
 */
class laabs
{
    use laabsModelTrait, laabsAppTrait;
    /* Constants */
    const NORMALIZATION_MAP = array(
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'Æ'=>'AE', 'æ'=>'ae', 
            'Þ'=>'B', 'þ'=>'b',
            'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'Ç'=>'C', 'ç'=>'c', 
            'Đ'=>'Dj', 'đ'=>'dj',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 
            'Ñ'=>'N', 'ñ'=>'n', 
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'œ'=>'oe', 'Œ'=>'OE',
            'Ŕ'=>'R', 'ŕ'=>'r',
            'Š'=>'S', 'š'=>'s', 'ß'=>'Ss', 
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 
            'Ý'=>'Y', 'ý'=>'y', 'ý'=>'y', 'ÿ'=>'y',
            'Ž'=>'Z', 'ž'=>'z', 
        );

    /* Properties */
    /**
     * Other autoloads methods registered if base fails
     */
    protected $autoload;

    /**
     * The instance identifier
     * @var string
     */
    protected static $instance;

    /**
     * The log file handler or 'syslog'
     * @var mixed
     */
    protected static $log;

    /**
     * Server config
     */
    protected static $_SERVER;

    /**
     * Memcache
     * @var object
     */
    protected static $memcache;

    /**
     * cache
     * @var array
     */
    protected static $cache;

    /**
     * The root directory
     * @var string
     */
    protected static $rootdir;

    /**
     * The files for autoload
     * @var array
     */
    protected static $srcfiles;

    /* Methods */

    /**
     * Register the autoloader
     */
    public static function init()
    {
        $_SERVER['LAABS-INIT-TIME'] = microtime(true);

        chdir('..');
        static::$rootdir = getcwd();

        // Change current dir to src
        chdir('./src');

        static::$instance = static::getInstanceName();
        
        // Register base autoload
        \spl_autoload_register(static::getAutoload());

        static::preload();

        // Start session
        if (isset($_SERVER['LAABS_SESSION_START']) && $_SERVER['LAABS_SESSION_START'] == 'On') {
            \core\Globals\Session::start();
        }

        // Start cache strategies
        if (($memcacheServer = \laabs::getMemCacheServer()) && !empty(static::$instance)) {
            $svrName = strtok($memcacheServer, ":");
            $svrPort = strtok(":");
            static::$memcache = new \Memcache();
            if (!static::$memcache->connect($svrName, $svrPort)) {
                throw new Exception("Unable to connect to MemCache server at address $svrName:$svrPort");
            }
            if (isset($_REQUEST['LAABS_CACHE_FLUSH'])) {
                $flushed = static::$memcache->flush();
            }
        }

        if (isset($_REQUEST['LAABS_CACHE_FLUSH'])) {
            static::$cache = array();

            if (isset($_SESSION['LAABS_CACHE'])) {
                unset($_SESSION['LAABS_CACHE']);
            }

        }



        // Try pre-load of classes
        /*if ($coreClasses = static::getCache('LAABS_CORE_CLASSES')) {
            foreach ($coreClasses as $class => $classfile) {
                require_once($classfile);
            }
        }*/

        /* Declare error handler */
        if ($errorHandler = \laabs::getErrorHandler()) {
            set_error_handler($errorHandler);
        }

        /* Declare exception handler */
        if ($exceptionHandler = \laabs::getExceptionHandler()) {
            set_exception_handler($exceptionHandler);
        }

        /* Start buffer output if clean buffer requested */
        if (\laabs::getBufferMode() != LAABS_BUFFER_NONE) {
            $callback = \laabs::getBufferCallback();
            ob_start($callback);
        }

        if ($phpIni = \laabs::getPhpConfiguration()) {
            if (!is_file($phpIni)) {
                throw new Exception("Php runtime configuration file $phpIni not found");
            }

            $phpConf = parse_ini_file($phpIni, false);

            if (count($phpConf) > 0) {
                foreach ($phpConf as $varname => $newvalue) {
                    if (\ini_set($varname, $newvalue) === false) {
                        throw new Exception("Could not set directive '$varname' to '$newvalue'. Possible reasons are : variable name is invalid, new value is invalid or the variable doesn't accept this change mode. Check PHP documentation at www.php.net/manual/en/ini.list.php");
                    }
                }
            }
        }

        //static::getCookies();
    }

    protected static function preload()
    {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'ReadonlyTrait.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Kernel/KernelInterface.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Kernel/AbstractKernel.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Globals/AbstractGlobal.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Request/RequestInterface.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Request/AbstractRequest.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Response/ResponseInterface.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Response/AbstractResponse.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/DocCommentTrait.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/abstractContainer.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/abstractClass.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/abstractMethod.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/abstractProperty.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Bundle.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Dependency.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Service.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Extensions.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Extension.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Exception.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Method.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Reflection/Parameter.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Observer/Dispatcher.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Observer/Pool.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Route/AbstractRouter.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Route/ContainerRouter.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Route/BundleRouter.php');

        require_once(__DIR__.DIRECTORY_SEPARATOR.'Configuration/Section.php');
        require_once(__DIR__.DIRECTORY_SEPARATOR.'Configuration/Configuration.php');
    }

    /**
     *  Class autoloader
     *  Requires the source file of a given class based on its namespace\name
     *  @param string $class the name of the class to load
     *
     * @return bool
     */
    public static function autoload($class)
    {
        // Build class file path
        $classfile = str_replace(LAABS_NS_SEPARATOR, DIRECTORY_SEPARATOR, $class).'.php';

        switch (strtok($class, LAABS_NS_SEPARATOR)) {
            case LAABS_CORE:
                $classfile = static::$rootdir.DIRECTORY_SEPARATOR.$classfile;
                break;

            case LAABS_DEPENDENCY:
                $classfile = static::$rootdir.DIRECTORY_SEPARATOR.$classfile;
                if (!file_exists($classfile)) {
                    // When a function checks class file exists (for class_exists, class_uses functions ...)
                    return false;
                }
                break;

            case LAABS_BUNDLE:
            case LAABS_PRESENTATION:
            case LAABS_EXTENSION:
                if (!file_exists($classfile)) {
                    // If interface, try case insensitive search on name
                    if (substr($class, -9) == LAABS_INTERFACE) {
                        $found = false;

                        $lclassfile = strtolower($classfile);

                        $nsdir = dirname($classfile);
                        if (!is_dir($nsdir)) {
                            return false;
                        }
                        if (!isset(static::$srcfiles[$nsdir])) {
                            $nsfiles = scandir($nsdir);
                            foreach ($nsfiles as $nsfile) {
                                static::$srcfiles[$nsdir][] = $nsdir.DIRECTORY_SEPARATOR.$nsfile;
                            }
                        }
                        //$nsfiles = glob($nsdir . DIRECTORY_SEPARATOR . '*');
                        $srcfiles = static::$srcfiles[$nsdir];
                        foreach ($srcfiles as $srcfile) {
                            if (strtolower($srcfile) == $lclassfile) {
                                $classfile = $srcfile;
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            return false;
                        }
                    } else {
                        // When a function checks class file exists (for class_exists, class_uses functions ...)
                        return false;
                    }
                }

                break;

            default:
                return false;
        }

        require_once $classfile;

        return true;
    }

    /**
     *  Fallback exception handler
     *  Catches all uncaught exceptions and sends back a formatted message in requested response format
     *  @param object $exception An uncaught exception to handle
     */
    public static function exceptionHandler($exception)
    {
        \core\Observer\Dispatcher::notify(LAABS_EXCEPTION, $exception);
        static::log($exception->getMessage(), \E_ERROR);
        if ($errorUri = static::getErrorUri()) {
            if ($kernel = static::kernel()) {
                if (\laabs::presentation()) {
                    $contents = \laabs::presentView($errorUri, array($exception));
                } else {

                }
                /*$route = 'READ '. $errorUri;
                $type = $kernel->response->contentType;
                if (!$type) {
                    $type = 'text';
                }*/
                try {
                    $kernel->response->setCode(500);
                    $kernel->response->setBody($contents);
                    $kernel->response->send();
                    $kernel::end();
                } catch (\Exception $e) {
                    static::log($e->getMessage(), \E_ERROR);
                }
            }
        }

        exit;
    }

    /**
     *   Fallback error handler
     *   Catches all errors and sends back a formatted message in requested reponse format
     *   @param int    $errno      The error level (E_STRICT, E_WARNING, etc...)
     *   @param string $errstr     The error message
     *   @param string $errfile    The file where error occured
     *   @param int    $errline    The line where error occured
     *   @param mixed  $errcontext The context of data
     */
    public static function errorHandler($errno, $errstr, $errfile = null, $errline = null, $errcontext = null)
    {
        $error = new \core\Error($errstr, null, $errno, $errfile, $errline, $errcontext);
        \core\Observer\Dispatcher::notify(LAABS_ERROR, $error);
        static::log($error->getMessage(), \E_ERROR);
        switch ($error->getCode()) {
            case E_ERROR: // 1
            case E_CORE_ERROR: // 16
            case E_COMPILE_ERROR: // 64
            case E_USER_ERROR: // 256
            case E_RECOVERABLE_ERROR: // 4096
            case E_PARSE: // 4
                if ($errorUri = static::getErrorUri()) {
                    if ($kernel = static::kernel()) {
                        $route = 'READ '.$errorUri;
                        $type = $kernel->response->contentType;
                        if (!$type) {
                            $type = 'text';
                        }

                        try {
                            //$kernel->response->setCode(500);
                            $kernel->response->setBody(static::callOutputRoute($route, $type, $error));
                            $kernel->response->send();
                            $response->send();
                        } catch (\Exception $e) {
                            static::log($e->getMessage(), \E_ERROR);
                        }
                    }
                }
        }
    }

    public static function catchableErrorHandler()
    {

    }

    /**
     * Get the current running Kernel
     * @return object The Kernel
     */
    public static function kernel()
    {
        return \core\Kernel\AbstractKernel::get();
    }

    /**
     * Decode an object serialized on an ini file
     * @param string  $value The ini string to convert
     * @param boolean $assoc Return associative array instead of objects
     *
     * @return mixed
     */
    public static function decodeIni($value, $assoc = true)
    {
        // Replace single quotes by double (for json compliance) except if escaped
        $value = preg_replace('#(?<![\\\\])\'#', '"', $value);

        // Unescape quotes
        $value = preg_replace("#\\\\'#", "'", $value);

        // escape backslahes
        $value = preg_replace("#\\\\#", "\\\\\\\\", $value);

        // unescape double-quotes
        $value = preg_replace('#\\\\"#', '"', $value);

        // Remove double-quotes enclosing true/false values and convert On/Off
        $value = preg_replace('#"(on|true)"#i', 'true', $value);
        $value = preg_replace('#"(off|false)"#i', 'false', $value);

        $decodedValue = json_decode($value, $assoc);

        if (is_null($decodedValue)) {
            switch (json_last_error()) {
                /*case JSON_ERROR_NONE:
                    return $decodedValue;*/
                case JSON_ERROR_DEPTH:
                    $error = 'Maximal depth reached';
                    break;

                /*case JSON_ERROR_STATE_MISMATCH:
                    $error = 'State mismatch error'; 
                    break;

                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Control character error'; 
                    break;*/

                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error: malformed JSON';
                    break;

                case JSON_ERROR_UTF8:
                    $error = 'Invalid UTF-8 characters';
                    break;

                default:
                    $error = 'Unknown error';
            }

            $displayValue = $value;
            if (strlen($displayValue) > 1000) {
                $displayValue = substr($displayValue, 0, 997)."...";
            }
            throw new \Exception("Error decoding complex ini directive [$error] '$displayValue'");
        }

        return $decodedValue;
    }

    /**
     * Generates a Laabs debug context
     * @param bool $traceCore Trace code components or only source and dependencies
     *
     * @return array The backtrace of routes
     */
    public static function backtrace($traceCore = false)
    {
        $phpTrace = debug_backtrace(0);
        $trace = array();

        /*
            (array) of
                (string) function
                (int)    line
                (string) file
                (array)  args
                if OOP:
                (string) class
                (object) object
                (string) type (:: | ->)
        */

        $phpCurrent = reset($phpTrace);
        while ($phpStep = next($phpTrace)) {
            $step = false;
            if (isset($phpStep['class'])) {
                $step = array();

                if (isset($phpStep['file'])) {
                    $step['file'] = $phpStep['file'];
                }
                if (isset($phpStep['line'])) {
                    $step['line'] = $phpStep['line'];
                }

                $classParser = static::parseClass($phpStep['class']);
                $step = array_merge($step, $classParser);

                if (isset($classParser[LAABS_BUNDLE])) {
                    if (isset($classParser[LAABS_CONTROLLER])) {
                        $step[LAABS_CONTROLLER] = $classParser[LAABS_BUNDLE]
                            .LAABS_URI_SEPARATOR.$classParser[LAABS_CONTROLLER];
                        $methodName = LAABS_ACTION;
                    } elseif (isset($classParser[LAABS_PARSER])) {
                        $step[LAABS_PARSER] = $classParser[LAABS_BUNDLE]
                            .LAABS_URI_SEPARATOR.$classParser[LAABS_PARSER];
                        $methodName = LAABS_INPUT;
                    } elseif (isset($classParser[LAABS_SERIALIZER])) {
                        $step[LAABS_SERIALIZER] = $classParser[LAABS_BUNDLE]
                            .LAABS_URI_SEPARATOR.$classParser[LAABS_SERIALIZER];
                        $methodName = LAABS_OUTPUT;
                    } else {
                        $step[LAABS_SERVICE] = $classParser[LAABS_BUNDLE]
                            .LAABS_URI_SEPARATOR.$classParser[LAABS_SERVICE];
                        $methodName = LAABS_METHOD;
                    }
                } elseif (isset($classParser[LAABS_DEPENDENCY])) {
                    $step[LAABS_SERVICE] = $classParser[LAABS_DEPENDENCY]
                        .LAABS_URI_SEPARATOR.$classParser[LAABS_SERVICE];
                    $methodName = LAABS_METHOD;
                }

                $step[$methodName] = $phpStep['function'];
            } else {
                $step['function'] = $phpStep['function'];
            }
            if ($step) {
                foreach ($phpStep['args'] as $phpArg) {
                    if (is_scalar($phpArg)) {
                        $step['args'][] = $phpArg;
                    } else {
                        $step['args'][] = "__non_scalar_argument__";
                    }
                }
                $trace[] = $step;
            }
        }

        return $trace;

    }

    /**
     * Analyses a class name and returns an associative array of components
     * @param string $name The full name of the class
     *
     * @return array An associative array of component types and names
     */
    public static function parseClass($name)
    {
        if ($name[0] == LAABS_NS_SEPARATOR) {
            $name = substr($name, 1);
        }
        $steps = explode(LAABS_NS_SEPARATOR, $name);
        $parser = array();
        /*
            core/<component>
            core/laabs
            bundle/<bundle>
                /Controller/<controller>
                /Service/<service>
                /Exception/<exception>
            ext/<ext>
                bundle/...
            dependency/<dependency>
                /<service>
                /Adapter/<adapter>/<service>
        */

        // core/<component> | ext/<ext> | bundle/<bundle> | dependency/<dependency>
        $rootType = array_shift($steps);
        $rootName = array_shift($steps);
        $parser[$rootType] = $rootName;

        switch ($rootType) {
            case LAABS_EXTENSION:
                $containerType = array_shift($steps);
                $containerName = array_shift($steps);
                $parser[$containerType] = $containerName;
                // Continue after extension parsed

            case LAABS_BUNDLE:
            case LAABS_PRESENTATION:
                $componentType = array_shift($steps);
                switch ($componentType) {
                    case LAABS_CONTROLLER:
                    case LAABS_MODEL:
                    case LAABS_MESSAGE:
                    case LAABS_OBSERVER:
                    case LAABS_EXCEPTION:
                        $componentName = array_shift($steps);
                        $parser[$componentType] = $componentName;
                        break;

                    case LAABS_PARSER:
                    case LAABS_SERIALIZER:
                        $componentAdapter = array_shift($steps);
                        $componentName = array_shift($steps);
                        $parser[$componentType] = $componentName;
                        $parser[LAABS_ADAPTER] = $componentAdapter;
                        break;

                    case LAABS_PRESENTER:
                    case LAABS_USER_STORY:
                        $parser[$componentType] = \laabs\implode(LAABS_URI_SEPARATOR, $steps);
                        break;

                    default:
                        if (count($steps) > 0) {
                            $parser[LAABS_SERVICE] = $componentType.LAABS_URI_SEPARATOR.\laabs\implode(LAABS_URI_SEPARATOR, $steps);
                        } else {
                            $parser[LAABS_SERVICE] = $componentType;
                        }
                }
                break;

            case LAABS_DEPENDENCY:
                // Adapter/<adapter>/<service> |  <service>
                $next = array_shift($steps);
                if ($next == LAABS_ADAPTER) {
                    $componentAdapter = array_shift($steps);
                    $parser[$next] = $componentAdapter;
                    $parser[LAABS_SERVICE] = implode(LAABS_URI_SEPARATOR, $steps);
                } else {
                    if (count($steps) > 0) {
                        $parser[LAABS_SERVICE] = $next.LAABS_URI_SEPARATOR.\laabs\implode(LAABS_URI_SEPARATOR, $steps);
                    } else {
                        $parser[LAABS_SERVICE] = $next;
                    }
                }
                break;

            case LAABS_CORE:
                if (count($steps)) {
                    $parser['Class'] = implode(LAABS_URI_SEPARATOR, $steps);
                }
        }

        return $parser;
    }

    /**
     * Returns the delay between server request and current time
     * @return float The delay in microseconds
     */
    public static function requestDelay()
    {
        return (float) number_format(( microtime(true) - static::getRequestTime(true) ), 3);
    }

    /**
     * Creates a public resource on the web root directory if not already there.
     * The new file name will be a hash of the content, allowing a pre-check and avoiding copy of content if file already exists
     * @param string $content The content of the file to be created
     * @param string $path    The relative path to create the file (from web root).
     *
     * @return string The uri to access the resource from the web
     */
    public static function createPublicResource($content, $path = LAABS_TMP)
    {
        if (is_scalar($content)) {
            $uid = hash('md5', $content);
        } else {
            $uid = \laabs\uniqid();
        }

        $dir = "..".DIRECTORY_SEPARATOR.LAABS_WEB;
        if ($path) {
            $dir .= DIRECTORY_SEPARATOR.$path;
        }

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777)) {
                throw new Exception("Unable to make directory '$dir'");
            }
        }

        $file = $dir.DIRECTORY_SEPARATOR.$uid;

        $pathUri = str_replace(DIRECTORY_SEPARATOR, LAABS_URI_SEPARATOR, $path);
        $uri = LAABS_URI_SEPARATOR.$pathUri.LAABS_URI_SEPARATOR.$uid;

        if (!is_file($file)) {
            if (is_scalar($content)) {
                if (!file_put_contents($file, $content)) {
                    throw new Exception("Unable to copy resource to '$file'");
                }
            } elseif (is_resource($content)) {
                $fp = fopen($file, 'w');
                stream_copy_to_stream($content, $fp);
                fclose($fp);
            }
        }

        return $uri;
    }

    /**
     * Check if a public resource exist on the web root directory.
     * The new file name will be a hash of the content, allowing a pre-check and avoiding copy of content if file already exists
     * @param string $path The relative path to create the file (from web root).
     *
     * @return string The uri to access the resource from the web
     */
    public static function hasPublicResource($path = LAABS_TMP)
    {
        $dir = "..".DIRECTORY_SEPARATOR.LAABS_WEB;
        if ($path) {
            $dir .= DIRECTORY_SEPARATOR.$path;
        }

        if (!\laabs\file_exists($dir)) {
            return false;
        }

        return true;
    }

    /**
     * Create a stream resource in memory with a given content
     * @param string $contents
     *
     * @return resource
     * @author
     **/
    public static function createMemoryStream($contents)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    /**
     * Create a stream resource in temp with a given content
     * @param string $contents
     *
     * @return resource
     * @author
     **/
    public static function createTempStream($contents)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    /**
     * Log an event into laabs log
     * @param string  $message
     * @param integer $level
     *
     * @return void
     */
    public static function log($message, $level = 0)
    {
        if (!isset(static::$log)) {
            $log = static::getLog();
            if ($log == 'syslog') {
                openlog("LAABS ".static::getApp(), LOG_ODELAY, LOG_USER);
                static::$log = 'syslog';
            } else {
                static::$log = fopen($log, "a");
            }
        }

        if (static::$log == 'syslog') {
            syslog((float) $level, $message);
        } else {
            fwrite(static::$log, "[".\laabs\date()."] (".$level.') '.$message.PHP_EOL);
        }
    }

    /**
     * Close thecurrent log
     */
    public static function closeLog()
    {
        if (isset(static::$log)) {
            if (static::$log == 'syslog') {
                closelog();
            } else {
                fclose(static::$log);
            }
        }
    }


    /**
     * Store a value in cache
     * @param mixed   $key    The reference
     * @param mixed   $value  The value
     * @param integer $expire The time in seconds before the cached value expires
     *
     * @return string
     */
    public static function setCache($key, $value, $expire = false)
    {
        // Add instance name to key to prevent mutli-app instane collisions
        $key = static::$instance.LAABS_URI_SEPARATOR.$key;

        // Runtime cash (faster)
        static::$cache[$key] = $value;

        if ($expire === false) {
            $expire = \laabs::getMemCacheExpire();
        }

        // Memcache (persistant)
        if (isset(static::$memcache)) {
            return static::$memcache->set($key, $value, 0, $expire);
        } /*else {
            $expiration = time() + $expire;
            $_SESSION['LAABS_CACHE'][$key] = array($value, $expiration);
        }*/
    }

    /**
     * Store a value in cache
     * @param mixed $key The reference
     *
     * @return mixed The value
     */
    public static function getCache($key)
    {
        $key = static::$instance.LAABS_URI_SEPARATOR.$key;

        // Check runtime cache
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        // Check mem cache
        if (isset(static::$memcache) && ($value = static::$memcache->get($key))) {
            static::$cache[$key] = $value;

            return $value;
        }

        // Check session
        if (isset($_SESSION['LAABS_CACHE'][$key])) {
            if ($_SESSION['LAABS_CACHE'][$key][1] <= time()) {
                $value = $_SESSION['LAABS_CACHE'][$key][0];
                static::$cache[$key] = $value;

                return $value;
            } else {
                unset($_SESSION['LAABS_CACHE'][$key]);
            }
        }
    }

    /**
     * Clear tokens
     */
    public static function clearTokens()
    {
        foreach ($_COOKIE as $cookieName => $value) {
            if (strpos($cookieName, 'LAABS-') === 0) {
                setcookie($cookieName, "", time()-3600, '/');
            }
        }

        unset($GLOBALS["TOKEN"]);
    }

    /**
     * Get all tokens
     *
     * @return array
     */
    public static function getTokens()
    {
        foreach ($_COOKIE as $cookieName => $value) {
            if (strtok($cookieName, '-') == 'LAABS-') {
                $key = static::getCryptKey();
                $jsonToken = static::decrypt(base64_decode($value), $key);
                $token = \json_decode(trim($jsonToken));
                $GLOBALS["TOKEN"][strtok('')] = $token;
            }
        }

        return $GLOBALS["TOKEN"];
    }

    /**
     * Get a token by name
     * @param string $name  The name of the token
     * @param string $style The token location
     *
     * @return mixed The token data
     */
    public static function getToken($name, $style=LAABS_IN_COOKIE)
    {
        $token = null;

        if (isset($GLOBALS["TOKEN"][$name])) {
            $token = $GLOBALS["TOKEN"][$name];
        } else {
            switch ($style) {
                case LAABS_IN_HEADER:
                    $headerName = 'X-Laabs-'.static::toName($name);
                    $token = \laabs::getHeaderToken($headerName);
                    break;

                case LAABS_IN_COOKIE:
                default:
                    $cookieName = 'LAABS-'.static::toName($name);
                    $token = \laabs::getCookieToken($cookieName);
            }
        }

        if (empty($token)) {
            return null;
        }

        // Expired token
        if ($token->expiration != 0 && $token->expiration < time()) {
            static::unsetToken($name, $style);

            return null;
        }

        return $token->data;
    }

    private static function getCookieToken($cookieName)
    {
        if (isset($_COOKIE[$cookieName])) {
            $key = static::getCryptKey();

            $b64Token = base64_decode($_COOKIE[$cookieName]);

            $jsonToken = static::decrypt($b64Token, $key);

            $token = \json_decode(trim($jsonToken));

            // Not a token cookie
            if (!is_object($token) || !isset($token->expiration)) {
                return null;
            }

        } else {
            return null;
        }

        return $token;
    }

    private static function getHeaderToken($headerName)
    {
        $token = null;

        if (empty(\laabs::kernel()->request->headers[$headerName])) {
            return null;
        }

        $tokenUrlEncoded = \laabs::kernel()->request->headers[$headerName];
        $key = static::getCryptKey();

        $token = urldecode($tokenUrlEncoded);
        $b64Token = base64_decode($token);
        $jsonToken = static::decrypt($b64Token, $key);

        $token = \json_decode(trim($jsonToken));

        return $token;
    }

    /**
     * Create a new token
     * @param string  $name       The name of the token
     * @param object  $data       The object to save in the token
     * @param int     $expiration The cookie and token expiration time in second (1 hour = 3600, 1 day = 86400)
     * @param bbolean $httpOnly   Set cookie only in http
     *
     * @return boolean The result of the token creation
     */
    public static function setToken($name, $data, $expiration = 0, $httpOnly = true)
    {
        $cookieName = 'LAABS-'.static::toName($name);

        if (!empty($expiration)) {
            $expirationTime = time() + $expiration;
        } else {
            $expirationTime = 0;
        }
        $token = new \core\token($data, $expirationTime);

        $jsonToken = \json_encode($token);
        $cryptedToken = static::encrypt($jsonToken, static::getCryptKey());
        $cookieToken = base64_encode($cryptedToken);

        $secure = (isset($_SERVER['LAABS_SECURE_COOKIE']) && $_SERVER['LAABS_SECURE_COOKIE'] == "On");

        setcookie($cookieName, $cookieToken, $expirationTime, '/', null, $secure, $httpOnly);

        $GLOBALS["TOKEN"][$name] = json_decode($jsonToken);

        return true;
    }

    /**
     * Delete a new cookie
     * @param string $name  The name of the token
     * @param string $style The location
     *
     * @return boolean The result of the cookie creation
     */
    public static function unsetToken($name, $style=LAABS_IN_COOKIE)
    {
        if (isset($GLOBALS["TOKEN"][$name])) {
            unset($GLOBALS["TOKEN"][$name]);
        }
        switch ($style) {
            case LAABS_IN_HEADER:
                return true;

            case LAABS_IN_COOKIE:
            default:
                $cookieName = 'LAABS-'.static::toName($name);
                setcookie($cookieName, "", time()-3600, '/');
                if (isset($_COOKIE[$cookieName])) {
                    unset($_COOKIE[$cookieName]);
                }
        }

        return true;
    }


    /**
     * Encrypt string
     * @param string $string
     * @param string $key
     *
     * @return string The encrypted string
     */
    public static function encrypt($string, $key)
    {
        $cipher = \laabs::getCryptCipher();

        if (extension_loaded('openssl')) {
            foreach (openssl_get_cipher_methods() as $method) {
                if (strtolower($cipher) == strtolower($method)) {
                    $message_padded = $string;
                    if (strlen($message_padded) % 8) {
                        $message_padded = str_pad($message_padded, strlen($message_padded) + 8 - strlen($message_padded) % 8, "\0");
                    }

                    return openssl_encrypt($message_padded, $method, $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, "12345678");
                }
            }
        }
        
        return static::RC4($string, $key);
    }

    /**
     * Decrypt string
     * @param string $string
     * @param string $key
     *
     * @return string The decrypted string
     */
    public static function decrypt($string, $key)
    {
        $cipher = \laabs::getCryptCipher();

        if (extension_loaded('openssl')) {
            foreach (openssl_get_cipher_methods() as $method) {
                if (strtolower($cipher) == strtolower($method)) {
                    return openssl_decrypt($string, $method, $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, "12345678");
                }
            }
        }

        return static::RC4($string, $key);
    }

    /**
     * Cipher
     * @param string $data The data to encrypt/decrypt
     * @param string $key  The secret key
     *
     * @return string
     */
    public static function RC4($data, $key)
    {
        $akey[] = '';
        $abox[] = '';

        $keyLength = strlen($key);
        $dataLength = strlen($data);

        for ($i = 0; $i < 256; $i++) {
            $akey[$i] = ord($key[$i % $keyLength]);
            $abox[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $abox[$i] + $akey[$i]) % 256;
            $tmp = $abox[$i];
            $abox[$i] = $abox[$j];
            $abox[$j] = $tmp;
        }

        $RC4 = '';

        for ($a = $j = $i = 0; $i < $dataLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $abox[$a]) % 256;

            $tmp = $abox[$a];
            $abox[$a] = $abox[$j];
            $abox[$j] = $tmp;

            $k = $abox[(($abox[$a] + $abox[$j]) % 256)];
            $RC4 .= chr(ord($data[$i]) ^ $k);

        }

        return $RC4;
    }

    /**
     * Test if a string starts with the specified prefix
     *
     * @param string $haystack The string
     * @param string $needle   The prefix to test
     *
     * @return boolean
     */
    public static function strStartsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Test if a string ends with the specified suffix
     *
     * @param string $haystack The string
     * @param string $needle   The suffix to test
     *
     * @return boolean
     */
    public static function strEndsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Encode a value
     * @param string $value The value to encode
     * @param string $type  The type of value: alpha, alnum, digit
     * @param string $base  The base for encoding = bin: 2, oct: 8, hex: 16
     *
     * @return string The code
     */
    public static function strEncode($value, $type, $base)
    {
        switch ($base) {
            case 'bin':
                $base = 2;
                break;

            case 'oct':
                $base = 8;
                break;

            case 'hex':
                $base = 16;
                break;

        }

        switch ($type) {
            case 'digit':
                $dec = preg_replace("/([^0-9]+)/", "", $value);

                return base_convert($dec, 10, $base);

            case 'alpha':
                $table = 'abcdefghijklmnopqrstuvwxyz';
                $value = strtolower(static::normalize($value));

                $bin = "";
                foreach (str_split($value, 1) as $char) {
                    $dec = strpos($table, $char);
                    $bin .= str_pad(base_convert($dec, 10, 2), 5, "0", STR_PAD_LEFT);
                }

                return base_convert($bin, 2, $base);

            case 'alnum':
                //$table = '0123456789abcdefghijklmnopqrstuvwxyz';
                $value = strtolower(static::normalize($value));
                /*$b36 = "";
                foreach (str_split($value, 1) as $char) {
                    $b36 .= strpos($table, $char);
                    //$bin .= str_pad(base_convert($dec, 10, 2), 6, "0", STR_PAD_LEFT);
                }*/
                return \base_convert($value, 36, $base);
        }
    }

    /**
     * Normalizes the input provided and returns the normalized string
     * @param string $string
     *
     * @return string The normaized string
     */
    public static function normalize($string)
    {
        return strtr($string, static::NORMALIZATION_MAP);
    }

    /**
     * Make a valid name from a string (A-Z a-z 0-9 - _)
     * @param string $string
     *
     * @return string The name
     */
    public static function toName($string)
    {
        $name = str_replace(' ', '-', $string);
        $name = static::normalize($name);
        $name = preg_replace("/[^A-Za-z0-9\-_]/", "_", $name);

        if (ctype_digit($name[0])) {
            $name = "_".substr($name, 1);
        }

        return $name;
    }

    /**
     * Send a REST request and receive a response
     * @param string $method
     * @param string $url
     * @param array  $args
     * @param array  $headers
     * @param string $body
     *
     * @return array The response headers and body
     */
    public static function callAPI($method, $url, $args=null, $headers=array(), $body=null)
    {
        $curl = curl_init();

        switch ($method) {
            case "CREATE":
                curl_setopt($curl, \CURLOPT_POST, 1);

                if ($args) {
                    curl_setopt($curl, \CURLOPT_POSTFIELDS, $args);
                }
                break;

            case "READ":
                if ($args) {
                    $url = sprintf("%s?%s", $url, http_build_query($args));
                }
                break;

            case "UPDATE":
                curl_setopt($curl, \CURLOPT_PUT, 1);
                if ($args) {
                    curl_setopt($curl, \CURLOPT_POSTFIELDS, $args);
                }
                break;

            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                if ($args) {
                    $url = sprintf("%s?%s", $url, http_build_query($args));
                }
                break;

            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                if ($args) {
                    $url = sprintf("%s?%s", $url, http_build_query($args));
                }
        }

        if ($body) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $cookies = array();
        foreach ($_COOKIE as $key => $value) {
            $cookies[] = $key.'='.$value;
        }
        curl_setopt($curl, CURLOPT_COOKIE, implode(';', $cookies));

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Prevent session conflict if client and server are on same host
        session_write_close();
        $response = curl_exec($curl);

        $httpResponse = new \core\Response\HttpResponse();

        $responseHeaderSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $responseHeaderBlock = substr($response, 0, $responseHeaderSize);
        $responseHeaders = explode("\n", $responseHeaderBlock);

        $return = current($responseHeaders);
        $httpVersion = strtok($return, " ");
        $httpResponse->setCode(strtok(" "));
        $httpResponse->setText(strtok(""));

        while ($responseHeader = next($responseHeaders)) {
            $name = strtok($responseHeader, ":");
            $value = trim(strtok(""));
            if (!empty($name) && !empty($value)) {
                $httpResponse->setHeader($name, $value);
            }
        }

        $httpResponse->guessResponseType();

        $httpResponse->setBody(substr($response, $responseHeaderSize));

        foreach ($httpResponse->headers as $name => $header) {
            if ($name == 'Set-Cookie') {
                if (!is_array($header)) {
                    $header = array($header);
                }

                foreach ($header as $cookie) {
                    foreach (explode(";", $cookie) as $i => $part) {
                        $arg = strtok($part, "=");
                        $val = strtok('');
                        if ($i == 0) {
                            $name = $arg;
                            $value = $val;
                        } else {
                            ${$arg} = $value;
                        }
                    }

                    if (strtok($name, '-') == 'LAABS') {
                        setcookie($name, $value, $expires, '/', null, false, true);
                    }
                }
            }
        }

        curl_close($curl);

        return $httpResponse;
    }

    /**
     * Set the response type
     * @param string $type
     */
    public static function setResponseType($type)
    {
        if ($kernel = static::kernel()) {
            $kernel->response->setType($type);

            if ($kernel->response->mode == 'http') {
                $kernel->response->setContentType($type);
            }
        }
    }

    /**
     * Set the response code
     * @param string $code
     */
    public static function setResponseCode($code)
    {
        if ($kernel = static::kernel()) {
            $kernel->response->setCode($code);
        }
    }

    /**
     * Set the response content count
     * @param integer $count
     */
    public static function setResponseCount($count)
    {
        if ($kernel = static::kernel()) {
            $kernel->response->setCount($count);
        }
    }

    /**
     * Set the response header
     * @param string $name
     * @param string $value
     */
    public static function setResponseHeader($name, $value)
    {
        if ($kernel = static::kernel()) {
            $kernel->response->setHeader($name, $value);
        }
    }

    /**
     * Set the request content count
     * @return integer $count
     */
    public static function getRequestMaxCount()
    {
        if ($kernel = static::kernel()) {
            $count = $kernel->request->maxCount;
        }

        return $count;
    }

    /**
     * Check if a string is a valid JSON
     * @param string $jsonString
     *
     * @return bool
     */
    public static function isJson($jsonString)
    {
        $value = json_decode($jsonString);

        return (json_last_error() == \JSON_ERROR_NONE);
    }

    /**
     * Sanitize path for filenames
     * @param string $filename
     * @param string $replacement
     *
     * @return string
     */
    public static function sanitizeFilename($filename, $replacement = "_")
    {
        $forbiddenChars = array_merge(array_map('chr', range(0, 31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
        $filename = str_replace($forbiddenChars, "_", $filename);

        return $filename;
    }

    /**
     * Trace calls in log
     * @param string $source
     * @param string $callable
     */
    public static function trace($source)
    {
        $delay = round(microtime(true) - $_SERVER['LAABS-INIT-TIME'], 6);
        $message = $source.' '.$delay.'s '.round(memory_get_usage()/1000000).'/'.round(memory_get_peak_usage()/1000000).'Mb';
        static::log($message);
    }
}
