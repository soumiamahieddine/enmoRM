<?php
/**
 * Class file for Input definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Input definitions
 * 
 * @extends \core\Reflection\Method
 */
class Input
    extends Method
{

    /* Constants */

    /* Properties */
    public $bundle;

    public $parser;
    
    /* Methods */
    /**
     * Constructs a new input instance
     * @param string $name             The name of input
     * @param string $class            The class of the input
     * @param string $serviceContainer The service container name
     */
    public function __construct($name, $class, $serviceContainer)
    {
        parent::__construct($name, $class, $serviceContainer);

        $this->bundle = \laabs\basename(\laabs\dirname(\laabs\dirname($class)));

        $this->parser = \laabs\basename($class);
    }

    /**
     * Parse the input
     * @param object $parser      The parser of the input
     * @param object $requestBody The request to parse
     * 
     * @return string Request body
     */
    public function parse($parser = null, $requestBody)
    {
        \core\Observer\Dispatcher::notify(LAABS_INPUT_PARSING, $this, $requestBody);

        $input = $this->invokeArgs($parser, array($requestBody));

        \core\Observer\Dispatcher::notify(LAABS_INPUT_ARGUMENTS, $input);

        return $input;
    }

}
