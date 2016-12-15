<?php
/**
 * Class file for User Input definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs User Input
 * 
 * @extends \core\Reflection\Method
 */
class UserInput
    extends Method
{

    /* Constants */

    /* Properties */
    public $presentation;

    public $composer;

    /* Methods */
    /**
     * Constructs a new message instance
     * @param string $name             The name of message
     * @param string $class            The class of the message
     * @param string $serviceContainer The service container
     */
    public function __construct($name, $class, $serviceContainer)
    {
        parent::__construct($name, $class, $serviceContainer);

        $this->presentation = \laabs::getPresentation();

        $this->composer = \laabs\basename($class);
    }

    /**
     * Compose the message
     * @param object $composerObject Composer of the message
     * @param string $requestBody    The request body to compose the message
     * @param array  $requestArgs    The associative array of request arguments
     * 
     * @return array
     */
    public function compose($composerObject = null, $requestBody = null, $requestArgs = array())
    {
        $args = array($requestBody, $requestArgs);
        \core\Observer\Dispatcher::notify(LAABS_INPUT_COMPOSITION, $this, $args);

        $userInput = $this->invokeArgs($composerObject, $args);

        $context = array($this);
        
        \core\Observer\Dispatcher::notify(LAABS_INPUT_MESSAGE, $userInput, $context);

        return $userInput;
    }

}
