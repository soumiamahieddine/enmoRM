<?php
/**
 * Class file for Action definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs Action
 * 
 * @extends \core\Reflection\Method
 */
class Action
    extends Method
{

    /* Constants */

    /* Properties */
    public $bundle;

    public $controller;

    /* Methods */
    /**
     * Constructs a new action instance
     * @param string $name             The name of action
     * @param string $class            The class of the action
     * @param string $serviceContainer The service container
     */
    public function __construct($name, $class, $serviceContainer)
    {
        parent::__construct($name, $class, $serviceContainer);

        $this->bundle = \laabs\basename(\laabs\dirname(\laabs\dirname($class)));

        $this->controller = \laabs\basename($class);
    }

    /**
     * Call the action
     * @param object $controllerObject Controller of the action
     * @param array  $passedArgs       Arguments array for the action
     * 
     * @return mixte 
     */
    public function call($controllerObject = null, array $passedArgs = null)
    {
        \core\Observer\Dispatcher::notify(LAABS_ACTION_CALL, $this, $passedArgs);

        $response = $this->invokeArgs($controllerObject, $passedArgs);

        $context = array($this);
        \core\Observer\Dispatcher::notify(LAABS_ACTION_RESPONSE, $response, $context);

        return $response;
    }

}
