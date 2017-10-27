<?php
/**
 * Class file for View definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs View
 * 
 * @extends \core\Reflection\Method
 */
class View
    extends Method
{

    /* Constants */

    /* Properties */
    public $presentation;

    public $presenter;

    /* Methods */
    /**
     * Constructs a new view instance
     * @param string $name             The name of view
     * @param string $class            The class of the view
     * @param string $serviceContainer The service container
     */
    public function __construct($name, $class, $serviceContainer)
    {
        parent::__construct($name, $class, $serviceContainer);

        $this->presentation = \laabs::getPresentation();

        $this->presenter = \laabs\basename($class);
    }

    /**
     * Call the view
     * @param object $presenterObject Controller of the view
     * @param array  $passedArgs      Arguments array for the view
     * 
     * @return mixte 
     */
    public function present($presenterObject = null, array $passedArgs = null)
    {
        \core\Observer\Dispatcher::notify(LAABS_VIEW_PRESENTATION, $this, $passedArgs);

        $view = $this->invokeArgs($presenterObject, $passedArgs);

        $context = array($this);
        
        \core\Observer\Dispatcher::notify(LAABS_VIEW_CONTENTS, $view, $context);

        return $view;
    }

}
