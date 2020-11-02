<?php
/**
 * Class file for Action definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs Command
 * 
 * @extends \core\Reflection\Method
 */
class UserCommand
    extends Method
{

    /* Constants */

    /* Properties */
    /**
     * The name of the domain
     * @var string
     */
    public $domain;

    /**
     * The name of the userStory
     * @var string
     */
    public $userStory;

    /**
     * The request method CREATE READ UPDATE DELETE...
     * @var string
     */
    public $method;

    /**
     * The requested path
     * @var string
     */
    public $path;

    /**
     * The requested path steps
     * @var array
     */
    public $steps;

    /**
     * The requested path variables
     * @var array
     */
    public $vars;

    /**
     * The pattern
     * @var string
     */
    public $pattern;

    /**
     * The args contained in path
     * @var string
     */
    public $args;

    /**
     * The uri of the endpoint service
     * @var array
     */
    public $services;

    /**
     * The name of the composer <domain>/<composer>/<userInput>
     * @var string
     */
    public $userInput;

    /**
     * The name of the view <domain>/<presenter>/<view>
     * @var string
     */
    public $view;


    /* Methods */
    /**
     * Constructs a new action instance
     * @param string $name      The name of action
     * @param string $class     The class of the action
     * @param string $userStory The path of service
     * @param string $domain    The service container
     */
    public function __construct($name, $class, $userStory, $domain)
    {
        $this->returnType = null;
        $this->parameters = null;
        $this->tags = null;
        $this->services = null;
        $this->pattern = null;
        $this->view = null;

        parent::__construct($name, $class, $domain);

        $this->domain = $domain;

        $this->userStory = $userStory;

        if (isset($this->tags['var'])) {
            foreach ($this->tags['var'] as $pos => $var) {
                preg_match("#(?<type>\w+)\s*(?<name>\w+).*#", $var, $patternMatch);
                $pattern = \laabs::getTypePattern($patternMatch['type']);
                if (!$pattern) {
                    $pattern = '[A-Za-z0-9\xC380-\xC3BC\-_\=]+';
                }
                $patterns[$patternMatch['name']] = $pattern;
            }
        }

        /*if (isset($this->tags['param'])) {
            $paramTags = $this->tags['param'];
            if (count($paramTags) == 1) {
                $paramType = strtok($this->tags['param'][0], " ");
                if (!\Laabs::isScalarType($paramType)) {
                    $this->userInput = $paramType;
                }
            }
        }*/

        if (isset($this->tags['return'])) {
            $this->view = strtok($this->tags['return'][0], " ");
        }
        
        if (isset($this->tags['uses'])) {
            foreach ($this->tags['uses'] as $uses) {
                preg_match('#(?<path>[^\s]+)\s*(?<name>\w+)?#', $uses, $service);
                if (isset($service['name'])) {
                    $name = $service['name'];
                    $this->services[$name] = trim($service['path']);
                } else {
                    $this->services[] = trim($service['path']);
                }
                
            }
        }

        // Compose path pattern
        $this->steps = preg_split('#([a-z]+|[A-Z][a-z0-9]+|_[A-Za-z][A-Za-z0-9]*_)#', $this->name, -1, PREG_SPLIT_DELIM_CAPTURE + PREG_SPLIT_NO_EMPTY);
        $this->method = array_shift($this->steps);

        if (count($this->steps)) {
            $parts = array();
            foreach ($this->steps as $i => $step) {
                if (fnmatch("_*_", $step)) {
                    $varname = substr($step, 1, -1);
                    $this->variables[$varname] = null;
                    if (isset($patterns[$varname])) {
                        $parts[$i] = '(?<' . $varname . '>' . $patterns[$varname] . ')';
                    } else {
                        $parts[$i] = '(?<' . $varname . '>[A-Za-z0-9\xc380-\xc3bc\-_\=]+)';
                    }
                } else {
                    $parts[] = $step;
                }
            }

            $this->pattern = "#^" . str_replace("/", "\\/", implode('/', $parts)) . "$#i";
        }   

    }

    /**
     * Match a command with method and uri
     * @param string $method
     * @param string $uri
     * 
     * @return object The command with variables
     */
    public function match($method='read', $uri='')
    {
        $this->args = null;
        // Check method
        if (isset($this->method) && $this->method != $method) {
            return;
        }

        // Check empty uri
        if (!isset($this->pattern) && !empty($uri)) {
            return;
        } 

        // Check uri VS path pattern
        if (isset($this->pattern) && !preg_match($this->pattern, $uri, $args)) {
            return;
        }

        $matched = new UserCommand($this->name, $this->class, $this->userStory, $this->domain);

        if (!empty($args)) {
            // Remove complete regexp match at offset 0
            array_shift($args);

            foreach ($args as $name => $value) {
                if (!empty($name) && ctype_alpha($name[0])) {
                    $matched->variables[$name] = $value;
                }
            }
        }

        return $matched;
    }

    /**
     * Get path
     * 
     * @return string
     */
    public function getName()
    {
        return $this->userStory . LAABS_URI_SEPARATOR . $this->name;
    } 

    /**
     * Get the declaring userStory, that can be a parent of the interface
     * 
     * @return UserStory
     */
    public function getDeclaringUserStory()
    {
        $interface = parent::getDeclaringClass();

        $name = str_replace(LAABS_NS_SEPARATOR, LAABS_URI_SEPARATOR, substr($interface->name, strpos($interface->name, 'UserStory') + 10, -strlen(LAABS_INTERFACE)));

        return new \core\Reflection\UserStory($name, $interface, \laabs::presentation());
    } 

    /**
     * Re route
     * @param string $qname
     * 
     * @return command
     */
    public function reRoute($qname)
    {
        $name = \laabs\basename($qname);
        $userStory = \laabs\dirname($qname);

        $this->__construct(
            $name, 
            str_replace(LAABS_URI_SEPARATOR, LAABS_NS_SEPARATOR, $this->domain . LAABS_NS_SEPARATOR . LAABS_USER_STORY . LAABS_NS_SEPARATOR . $userStory) . LAABS_INTERFACE, 
            $userStory,
            $this->domain
        );

        return $this;
    } 

    /**
     * Compose internal message from parsed request
     * @param array $requestArgs The request arguments
     * 
     * @return array The user Story command arguments
     */
    public function getMessage(array $requestArgs = array())
    {
        // Get message parts using route definition
        $parameters = $this->getParameters();
        $messageParts = array();

        foreach ($parameters as $parameter) {
            switch (true) {
                // Value available from request arguments or body: cast into message part type
                case isset($requestArgs[$parameter->name]) :
                    $value = $requestArgs[$parameter->name];
                    break;

                // Default value
                case $parameter->isDefaultValueAvailable():
                    $value = $parameter->getDefaultValue();
                    break;

                // Optional : null
                case $parameter->isOptional():
                    $value = null;
                    break;

                // No other case should raise an exception
                default:
                    // Throw exception
                    $value = null;
            }

            $messageParts[$parameter->name] = $value;
        }
       
        // Backward remove null values from array
        do {
            $arg = end($messageParts);
            if ($arg === null) {
                array_pop($messageParts);
            }
        } while ($arg === null && count($messageParts));


        return $messageParts;
    }

}
