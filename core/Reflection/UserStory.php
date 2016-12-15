<?php
/**
 * Class file for Interface definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for reflection userStory
 * 
 * @extends \core\Reflection\Service
 */
class UserStory
    extends abstractClass
{
    
    /* Constants */

    /* Properties */
    /**
     * The uri of the service
     * @var string
     */
    public $uri;

    /**
     * The container domain
     * @var string
     */
    public $domain;

    /**
     * The access tag : public / protected / private
     * @var string
     */
    public $access;

    /* Methods */
    /**
     * Constructor of the UI
     * @param string $name      The name of the UI
     * @param string $class     The class of the UI
     * @param object $container The UI container object
     */
    public function __construct($name, $class, $container)
    {
        $this->uri = $name;

        $this->domain = $container->instance;

        parent::__construct($class); 

        if (isset($this->tags['access'])) {
        //if (preg_match("#@access\s+(?<access>[^\s]+)#", $this->getDocComment(), $return)) {
            $this->access = $this->tags['access'][0];
        } else {
            $this->access = 'protected';
        }
    }

    /**
     * Getter for component name
     * 
     * @return string The value of the property
     */
    public function getName() 
    {
        return $this->uri;
    }

    /**
     * Getter for component name
     * 
     * @return string The value of the property
     */
    public function getShortName() 
    {
        return \laabs\basename($this->uri);
    }

    /**
     * Check if userStory is public, no user authentication needed
     * 
     * @return boolean
     */
    public function isPublic() 
    {
        return ($this->access == 'public');
    }

    /**
     * Check if userStory is protected by a user authentication
     * 
     * @return boolean
     */
    public function isProtected() 
    {
        return ($this->access == 'protected');
    }

    /**
     * Check if userStory is private and no longer available
     * 
     * @return boolean
     */
    public function isPrivate() 
    {
        return ($this->access == 'private');
    }

    /**
     * Check if command exists
     * @param string $name the name of the command
     * 
     * @return boolean
     */
    public function hasUserCommand($name)
    {
        return parent::hasMethod($name);
    }

    /**
     * Returns the UI commands
     * @param int $filter
     * 
     * @return array An array of command objects declared for the UI
     */
    public function getUserCommands($filter=null)
    {
        $reflectionMethods = parent::getMethods(Method::IS_PUBLIC & ~Method::IS_STATIC);
        $commands = array();

        for ($i=0, $l=count($reflectionMethods); $i<$l; $i++) {
            $reflectionMethod = $reflectionMethods[$i];

            $commands[] = new UserCommand($reflectionMethod->name, $this->name, $this->uri, $this->domain);
        }

        return $commands;
    }

    /**
     * Get a UI command declaration from its name
     * @param string $name The name of the command
     * 
     * @return object The Method object
     * 
     * @throws Exception if the command is not declared by the UI
     */
    public function getUserCommand($name)
    {
        if (!parent::hasMethod($name)) {
            throw new \core\Exception("Undefined user command '$this->domain/$this->name/$name'");
        }

        $command = new UserCommand($name, $this->name, $this->uri, $this->domain);

        if (!$command->isPublic()) {
            throw new \core\Exception("USer command '$name' is not public");
        }

        return $command;
    }

}
