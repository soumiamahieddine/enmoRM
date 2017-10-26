<?php
namespace core\Reflection;
/**
 * Reflection class for components : service, controller, type, parser, serializer, observer
 * 
 * @package Laabs
 */
abstract class abstractClass
    extends \ReflectionClass
{

    use \core\ReadonlyTrait,
        DocCommentTrait;

    /**
     * Constructor of the injection service
     * @param string $classname The class for reflection
     */
    public function __construct($classname)
    {
        parent::__construct($classname);

        $this->parseDocComment();
    }


    /**
     * Called for unserialization
     */
    public function __wakeup() 
    {
        parent::__construct($this->name);

        $this->parseDocComment();
    }

    /**
     * Check if class has a __toString method
     * @return bool
     */
    public function isStringifyable()
    {
        return $this->hasMethod('__toString');
    }

    /**
     * Check if class has a __construct method
     * @return bool
     */
    public function hasConstructor()
    {
        return ($this->hasMethod('__construct') || $this->hasMethod(\laabs\basename($this->name)));
    }

    /**
     * Check if class has a __invoke method
     * @return bool
     */
    public function isCallable()
    {
        return $this->hasMethod('__invoke');
    }

}