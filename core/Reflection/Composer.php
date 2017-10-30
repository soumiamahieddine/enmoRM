<?php
/**
 * Class file for Composer definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Composer definitions
 * 
 * @extends \core\Reflection\Service
 */
class Composer
    extends Service
{

    /* Constants */

    /* Properties */
    

    /* Methods */
    
    /**
     * Constructor of the composer
     * @param string $name         The name of the composer
     * @param string $classname    The class of the composer
     * @param object $presentation The presentation object
     */
    public function __construct($name, $classname, $presentation)
    {
        $uri = LAABS_COMPOSER . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $presentation);
    }

    /**
     * Checks if user input is defined on the composer
     * @param string $name The name of the user input
     * 
     * @return bool
     */
    public function hasUserInput($name)
    {
        return $this->hasMethod($name);
    }

    /**
     * Get all user inputs defined on the composer
     * 
     * @return array An array of all the \core\Reflection\Message objects
     */
    public function getUserInputs()
    {
        $reflectionMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $messages = array();
        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isAbstract()
            ) {
                continue;
            }

            $messages[] = new Message($reflectionMethod->name, $this->name, $this->container);
        }

        return $messages;
    }

    /**
     * Get a composer definition
     * @param string $name The name of the UserInput
     * 
     * @return object the \core\Reflection\UserInput object
     * 
     * @throws core\Reflection\Exception if the UserInput is unknown or not public
     */
    public function getUserInput($name)
    {
        if (!$this->hasUserInput($name)) {
            throw new \Exception("Undefined user input '$this->container/$this->name/$name'");
        }

        $message = new UserInput($name, $this->name, $this->container);

        if (!$message->isPublic()) {
            throw new \Exception("User input '$name' is not public");
        }

        return $message;
    }

}
