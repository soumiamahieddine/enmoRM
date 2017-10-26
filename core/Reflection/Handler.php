<?php
/**
 * Class file for observer Handler definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs Handler
 * 
 * @extends \core\Reflection\Method
 */
class Handler
    extends Method
{

    /* Constants */

    /* Properties */
    public $subject;

    /* Methods */
    /**
     * Constructs a new handler instance
     * @param string $name             The name of handler
     * @param string $class            The class of the handler
     * @param string $serviceContainer The service container
     */
    public function __construct($name, $class, $serviceContainer)
    {
        parent::__construct($name, $class, $serviceContainer);

        // Set observed subject
        $docComment = $this->getDocComment();
        if (isset($this->tags['subject'])) { 
            $subject = $this->tags['subject'][0];

            if (defined($subject)) {
                $subject = constant($subject);
            }
            $this->subject = $subject;
        }
    }

    /**
     * Call the handler
     * @param object $observerObject Observer of the handler
     * @param array  $passedArgs     Arguments array for the handler
     * 
     * @return mixte 
     */
    public function notify($observerObject = null, array $passedArgs = null)
    {
        $response = $this->invokeArgs($observerObject, $passedArgs);

        return $response;
    }

}
