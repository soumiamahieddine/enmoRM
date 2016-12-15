<?php
namespace core;

/**
 * Class for variables in uris, routes and other query strings
 * 
 * @package Core
 * 
 **/
class Variable
{
    use \core\ReadonlyTrait;

    /* Constants */

    /* Properties */
    public $name;
    public $type;
    public $length;
    public $source;
    public $uri;

    /* Methods */
    /**
     * Constructor for the variable
     * @param string $name 
     * @param string $type 
     * @param string $length 
     * @param string $source 
     * @param string $uri 
     * 
     * @return void
     **/
    public function __construct($name, $type = false, $length = 0, $source = false, $uri = false)
    {
        $this->name = $name;
        if (strpos($name, ":")) {
            $this->source = strtok($name, ":");
            $this->uri = strtok(":");
        } else {
            $this->source = false;
            $this->uri = strtok($name, ":");
        }
    }

    /**
     * Get the value of the variable
     * @param mixed $data The data context to get variable value from
     *
     * @return mixed The value
     **/
    public function getValue($data = null) 
    {
        switch($this->source) {
            case 'session':
                return \laabs::getSession($this->uri);
                break;

            case 'method':
                $methodRouter = new \core\Route\MethodRouter($this->uri);
                $service = $methodRouter->service;

                return $service->callMethod($data);
                
            case 'data':
            default:
                if (!$data) {
                    return $this->name;
                }
                $node = $data;
                preg_match_all('#(?<sep>\/|\.)?(?<name>(?:(?!\/|\.).)+)#', $this->uri, $steps, PREG_SET_ORDER);
                foreach ($steps as $step) {
                    if (($node = $this->getStep($step['name'], $step['sep'], $node)) == null) {
                        break;
                    }
                }
                return $node;
        }
    }

    /**
     * Get the value from a data path step
     * @param string $name The name of the step
     * @param string $sep  The separator with previous step
     * @param string $node The current node
     *
     * @return mixed The value
     **/
    protected function getStep($name, $sep, $node)
    {
        if (!$name) {
            return;
        }
        if ($sep == ".") {
            if (is_object($node) && isset($node->$name)) {
                $stepNode = $node->$name;
            } else { 
                return;
            }
        } else {
            if ((is_array($node) || $node instanceof \ArrayObject) && isset($node[$name])) {
                $stepNode = $node[$name];
            } else {
                return;
            }
        }

        return $stepNode;
    }
}
