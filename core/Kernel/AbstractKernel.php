<?php
/**
 * Class file for Laabs Kernel Abstract
 * @package core\Kernel
 */
namespace core\Kernel;
/**
 * Abstract Class for Laabs Kernel Abstract
 *
 * @uses core\ReadonlyTrait
 */
abstract class AbstractKernel
    implements KernelInterface
{
    use \core\ReadonlyTrait;
    /* Constants */

    /* Properties */
    /**
     * The instanciated Kernel singleton
     * @access protected
     * @var object $instance
     */
    protected static $instance;

    /**
     * The associated log
     * @access protected
     * @var resource $log
     */
    protected static $log;

    /**
     * The laabs Request object
     * @var object $request
     */
    public $request;

    /**
     * The laabs Response object
     * @var object $response
     */
    public $response;

    /* Methods */
    /**
     * Start a new Kernel instance singleton
     * @param string $requestMode
     * @param string $requestType
     * @param string $responseType
     * @param string $responseLanguage
     *
     * @return object instance
     *
     * @throws Exception if a Kernel has already been started
     */
    public static function start($requestMode=false, $requestType=false, $responseType=false, $responseLanguage=false)
    {
        if (self::started()) {
            throw new Exception("Kernel already started.");
        }

        $class = get_called_class();
        self::$instance = new $class($requestMode, $requestType, $responseType, $responseLanguage);

        self::$instance->initPackages();

        return self::$instance;

    }

    /**
     * Check the Kernel instance singleton
     *
     * @return bool
     */
    public static function started()
    {
        return self::$instance != null;
    }

    /**
     * End the Kernel instance singleton to allow a new start (for batch process that will run several kernels)
     */
    public static function end()
    {
        if (!self::started()) {
            //throw new Exception("Kernel is not started.");
            return;
        }

        static::$instance = null;

        switch(\laabs::getBufferMode()) {
            case LAABS_BUFFER_NONE:
                break;

            case LAABS_BUFFER_GET:
                ob_end_flush();
                break;

            case LAABS_BUFFER_CLEAN:
                ob_end_clean();
                break;
        }

        \laabs::closeLog();
    }

    /**
     * Get the Kernel instance singleton
     * @return object The Kernel object started
     * @throws Exception if no Kernel has been started yet
     */
    public static function get()
    {
        if (self::started()) {
            return self::$instance;
        }

        throw new Exception("Kernel has no been started.");
    }

    /**
     * Constructor for a new Kernel instance singleton
     *  * Initalise PHP
     *  * Instanciates the Request object
     *  * Instanciates the Response object
     * @param string $requestMode
     * @param string $requestType
     * @param string $responseType
     * @param string $responseLanguage
     */
    protected function __construct($requestMode=false, $requestType=false, $responseType=false, $responseLanguage=false)
    {
        /* Define constants from definitions */
        $this->defineConstants();

        $this->getRequest($requestMode, $requestType);

        $this->getResponse($responseType, $responseLanguage);
    }

    /**
     *  Check existence of a constants.php file at root of app/dependecies/bundles
     */
    protected function defineConstants()
    {       
        foreach (\laabs::getDependencies() as $dependency) {
            $dependencyFile = ".." . DIRECTORY_SEPARATOR . LAABS_DEPENDENCY . DIRECTORY_SEPARATOR . $dependency . DIRECTORY_SEPARATOR . 'constants.php';
            if (is_file($dependencyFile)) {
                require_once($dependencyFile);
            }
        }

        foreach (\laabs::getBundles() as $bundle) {
            $bundleFiles = \core\Reflection\Extensions::extendedPath(LAABS_BUNDLE . DIRECTORY_SEPARATOR . $bundle . DIRECTORY_SEPARATOR . 'constants.php', $unique = false);
            foreach ($bundleFiles as $bundleFile) {
                require_once($bundleFile);
            }
        }
    }

    /**
     *  Check existence of a config.inc.php file at root of app/dependecies/bundles
     */
    protected function initPackages()  
    {       
        foreach (\laabs::getDependencies() as $dependency) {
            $dependencyFile = ".." . DIRECTORY_SEPARATOR . LAABS_DEPENDENCY . DIRECTORY_SEPARATOR . $dependency . DIRECTORY_SEPARATOR . 'init.php';
            if (is_file($dependencyFile)) {           
                require_once($dependencyFile);
            }
        }

        foreach (\laabs::getBundles() as $bundle) {
            $bundleFiles = \core\Reflection\Extensions::extendedPath(LAABS_BUNDLE.DIRECTORY_SEPARATOR.$bundle . DIRECTORY_SEPARATOR . 'init.php', $unique = false);
            foreach ($bundleFiles as $bundleFile) {
                require_once($bundleFile);
            }
        }
    }

    /**
     * Create the Request object
     * @param mixed  $requestMode The code of request mode to create (http/cli) OR the request
     * @param string $requestType The contentType definition code used in request
     */
    protected function getRequest($requestMode=false, $requestType=false)
    {
        /* Get Request Mode cli / http */
        if (!$requestMode) {
            $requestMode = $this->guessRequestMode();
        }

        switch($requestMode) {
            case 'http':
                $this->request = new \core\Request\HttpRequest();
                break;

            case 'cli':
                $this->request = new \core\Request\CliRequest();
                break;

            default:
                $this->request = $requestMode;
        }

    }

    /**
     * Create the response object
     * @param string $responseType     The contentType definition code used for response
     * @param string $responseLanguage The ContentLanguage definition code for response
     */
    protected function getResponse($responseType=null, $responseLanguage=null)
    {
        switch($this->request->mode) {
            case 'http':
                $this->response = new \core\Response\HttpResponse();

                break;
            case 'cli':
                $this->response = new \core\Response\CliResponse();
                break;

            case 'php':
                $this->response = new \core\Response\PhpResponse();
                break;
        }

        /* Get response type */
        $this->response->setType($responseType);
        
        /* Get response lang */
        $this->response->setLanguage($responseLanguage);

        \core\Observer\Dispatcher::notify(LAABS_RESPONSE, $this->response);
    }

    /**
     * Guess the request mode (Http/Cli)
     * @return string The request mode "cli" or "http"
     */
    protected function guessRequestMode()
    {
        if (php_sapi_name() == 'cli'
        || array_key_exists('SHELL', $_ENV)
        || !isset($_SERVER['REQUEST_METHOD'])
        || defined('STDIN')
        ) {
            return 'cli';
        } else {
            return 'http';
        }
    }


    /**
     * Send response to client
     * @access protected
     */
    public function sendResponse()
    {
        // Buffer will return void if "LAABS_CLEAN_BUFFER" directive set for app
        $this->useBuffer();

        if (!is_null($this->response->body) && !is_scalar($this->response->body) && !is_resource($this->response->body)) {
            throw new \core\Exception("Response content can not be displayed");
        }

        $this->response->send();
    }

    /**
     * Use the output buffer if requested
     *
     * @return void
     * @author
     **/
    protected function useBuffer()
    {
        $buffer = false;
        switch(\laabs::getBufferMode()) {
            case LAABS_BUFFER_NONE:
                break;

            case LAABS_BUFFER_GET:
                $this->response->setBody(ob_get_clean() . $this->response->body);
                break;

            case LAABS_BUFFER_CLEAN:
                ob_clean();
                break;
        }
    }

    /**
     * Attach the observers to event dispatcher
     * @access protected
     */
    protected function attachObservers()
    {
        if (!\laabs::isServiceClient()) {
            $presentation = \laabs::presentation();
            
            foreach ($presentation->getObservers() as $observer) {
                self::attachObserver($observer);
            }
        }

        foreach (\laabs::bundles() as $bundle) {
            foreach ($bundle->getObservers() as $observer) {
                self::attachObserver($observer);
            }
        }
    }

    /**
     * Attach an observet to event dispatcher
     * @param object $observer
     * 
     * @access protected
     */
    protected function attachObserver($observer)
    {
        $observerObject = $observer->newInstance();
            
        foreach ($observer->getHandlers() as $handler) {
            \core\Observer\Dispatcher::attach(
                $observerObject,
                $handler->name,
                $handler->subject
            );
        }
    }

}