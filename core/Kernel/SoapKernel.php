<?php
/**
 * Class file for Laabs Dynamic Kernel
 * @package core\Kernel
 */
namespace core\Kernel;
/**
 * Class Laabs Soap Kernel
 *
 * @extends core\Kernel\AbstractKernel
 */
class SoapKernel
    extends AbstractKernel
{
    /* Constants */

    /* Properties */
    public $soapDisco;

    public $soapServer;

    public $__dispatch_map;

    public $__typedef;

    /**
     * The service path definition
     * @var \core\Reflection\Path
     */
    public $servicePath;

    /**
     * The Action router
     * @var \core\Route\ActionRouter
     */
    public $actionRouter;

    /* Methods */
    /**
     * Constructor
     */
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

        $this->getRequest();

        foreach (glob('../dependency/PEAR'.DIRECTORY_SEPARATOR.'*.php') as $phpfile) {
            require_once($phpfile);
        }

        foreach (glob('../dependency/PEAR/SOAP'.DIRECTORY_SEPARATOR.'*.php') as $phpfile) {
            require_once($phpfile);
        }

        //require_once 'SOAP/Disco.php';
        //require_once 'SOAP/Server.php';

        $this->soapServer = new \SOAP_Server();

        $this->soapServer->addObjectMap($this, 'urn:'.\laabs::getInstanceName());

        $this->soapDisco = new \SOAP_DISCO_Server($this->soapServer, \laabs::getInstanceName());
    }

    /**
     * Run the kernel to process request
     */
    public static function run()
    {
        self::$instance->attachObservers();

        $accountToken = \laabs::getToken('AUTH');

        /* Initalize components app/dependecy/bundle */
        $data = file_get_contents("php://input");

        static::$instance->soapServer->service($data);
    }

    public function __dispatch($name)
    {
        if (!isset($this->__dispatch_map[$name])) {
            return false;
        }

        if (!isset($this->__dispatch_map[$name]['controller'])) {
            $this->__dispatch_map[$name]['namespace'] = 'urn:'.\laabs::getInstanceName();

            $pathRouter = new \core\Route\PathRouter($this->__dispatch_map[$name]['method']);
            
            $this->servicePath = $pathRouter->path;

            try {
                \core\Observer\Dispatcher::notify(LAABS_SERVICE_PATH, $this->servicePath);
            } catch (\bundle\auth\Exception\authenticationException $e) {
                http_response_code(401);
                header('WWW-Authenticate: Basic realm="'.\laabs::getInstanceName().'"');
                exit;
            } catch (\core\Exception\UnauthorizedException $e) {
                http_response_code(401);
                exit;
            } catch (\Exception $e) {
                http_response_code(500);
                var_dump($e->getMessage());
                exit;
            }

            $this->actionRouter = new \core\Route\ActionRouter($this->servicePath->action);

            $action = $this->actionRouter->action;
            $this->__dispatch_map[$name]['controller'] = $this->actionRouter->controller->newInstance();
            $this->__dispatch_map[$name]['path'] = $this->__dispatch_map[$name]['method'];
            $this->__dispatch_map[$name]['method'] = $action->name;
            
        }

        return $this->__dispatch_map[$name];
    }


}