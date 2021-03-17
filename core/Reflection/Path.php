<?php
/**
 * Class file for Action definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs service path
 *
 * @extends \core\Reflection\Method
 */
class Path
    extends abstractMethod
{

    /* Constants */

    /* Properties */
    /**
     * The name of the domain
     * @var string
     */
    public $domain;

    /**
     * The name of the interface
     * @var string
     */
    public $interface;

    /**
     * The request method CRETE READ UPDATE DELETE
     * @var string
     */
    public $method;

    /**
     * The requested path
     * @var string
     */
    public $path;

    /**
     * The pattern
     * @var string
     */
    public $pattern;

    /**
     * The uri of the action <bundle>/<component>/<method>
     * @var string
     */
    public $action;

    /**
     * The variables contained in path
     * @var array
     */
    public $variables = array();

    /**
     * The parameters of the method, used to extract variables from request uri
     * @var array
     */
    public $parameters;

    /* Methods */
    /**
     * Constructs a new action instance
     * @param string $name   The name of action
     * @param string $class  The class of the action
     * @param string $domain The service container
     */
    public function __construct($name, $class, $domain)
    {
        $this->returnType = null;
        $this->parameters = null;
        $this->tags = null;

        parent::__construct($class, $name);

        $this->domain = $domain;

        $this->interface = substr($class, strrpos($class, LAABS_NS_SEPARATOR)+1, -9);

        $parts = preg_split("#([a-z]+|[A-Z][a-z0-9]+|_[A-Za-z][A-Za-z0-9]*_)#", $this->name, -1, PREG_SPLIT_DELIM_CAPTURE + PREG_SPLIT_NO_EMPTY);
        $this->method = array_shift($parts);

        $patterns = array();
        if (isset($this->tags['var'])) {
        //if (preg_match_all("#@var (?<type>\w+)\s*(?<name>\w+).*#", $docComment, $patternMatches, PREG_SET_ORDER)) {
            foreach ($thid->tags['var'] as $pos => $var) {
                preg_match("#(?<type>\w+)\s*(?<name>\w+).*#", $var, $patternMatches);
                $pattern = \laabs::getTypePattern($patternMatch['type']);
                if (!$pattern) {
                    $pattern = '[A-Za-z0-9\xC380-\xC3BC\-_\=]+';
                }
                $patterns[$patternMatch['name']] = $pattern;
            }
        }

        $patternParts = array();
        foreach ($parts as $i => $part) {
            if (fnmatch("_*_", $part)) {
                $varname = substr($parts[$i], 1, -1);
                $this->variables[$varname] = null;
                if (isset($patterns[$varname])) {
                    $patternParts[$i] = '(?<' . $varname . '>' . $patterns[$varname] . ')';
                } else {
                    $patternParts[$i] = '(?<' . $varname . '>[A-Za-z0-9\xC380-\xC3BC\-_\=]+)';
                }

                $parts[$i] = "{" . $varname . "}";
            } else {
                $patternParts[$i] = $part;
            }
        }

        array_unshift($parts, $this->interface);
        array_unshift($parts, $this->domain);
        $this->path = implode('/', $parts);

        $this->pattern = implode('/', $patternParts);

        if (isset($this->tags['action'])) {
            $this->action = strtok($this->tags['action'][0], " ");
        }
    }

    /**
     * Match a path with method and uri
     * @param string $method
     * @param string $uri
     *
     * @return object The path with variables
     */
    public function match($method='read', $uri='')
    {
        //$this->variables = null;

        // Check method
        if (isset($this->method) && $this->method != $method) {
            return;
        }

        // Check empty uri
        if (!isset($this->path) && !empty($uri)) {
            return;
        }

        // Check uri VS path pattern
        if (isset($this->path) && !@preg_match("#^" . str_replace("/", "\\/", $this->pattern) . "$#i", $uri, $args)) {
            return;
        }

        $matched = new Path($this->name, $this->class, $this->domain);

        if (!empty($args)) {
            // Remove complete regexp match at offset 0
            array_shift($args);

            foreach ($args as $name => $value) {
                if (!empty($name) && ctype_alpha($name)) {
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
        return $this->domain . LAABS_URI_SEPARATOR . $this->interface . LAABS_URI_SEPARATOR . $this->name;
    }

    /**
     * Reroute
     * @param \core\Reflection\Path $newPath
     *
     * @return Path
     */
    public function reroute($newPath)
    {
        $this->__construct($newPath->name, $newPath->class, $newPath->domain);

        return $this;
    }

    /**
     * Set a variable in path
     * @param string $name
     * @param mixed  $value
     */
    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Get variables
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Get message
     * @param array $requestArgs The received arguments
     *
     * @return array The service arguments
     */
    public function getMessage(array $requestArgs = array())
    {
        // Get message parts using route definition
        $parameters = $this->getParameters();
        $messageParts = array();
        foreach ($parameters as $i => $parameter) {
            switch (true) {
                // Value available from request arguments or body: cast into message part type
                case isset($requestArgs[$parameter->name]):
                    $messageParts[$parameter->name]  = \laabs::cast($requestArgs[$parameter->name], $parameter->getType(), true);
                    break;

                // Value available from request arguments or body: cast into message part type
                case isset($requestArgs[$i]):
                    $messageParts[$parameter->name] = \laabs::cast($requestArgs[$i], $parameter->getType(), true);
                    break;

                // Default value
                case $parameter->isDefaultValueAvailable():
                    $messageParts[$parameter->name] = $parameter->getDefaultValue();
                    break;

                // Optional : null
                case $parameter->isOptional():
                    $messageParts[$parameter->name] = null;
                    break;

                // No other case should raise an exception
                default:
                    // Throw exception
                    $messageParts[$parameter->name] = null;
            }
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
