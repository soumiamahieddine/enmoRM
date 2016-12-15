<?php
namespace core\Reflection;
/**
 * Reflection class for components : service, controller, type, parser, serializer, observer
 * 
 * @package Laabs
 */
abstract class abstractProperty
    extends \ReflectionProperty
{

    use \core\ReadonlyTrait,
        DocCommentTrait;

    /**
     * Constructor of the injection service
     * @param string $classname The class for reflection
     * @param string $name      The name of the property
     */
    public function __construct($classname, $name)
    {
        parent::__construct($classname, $name);

        $this->parseDocComment();
    }

    /**
     * Called for unserialization
     */
    public function __wakeup() 
    {
        parent::__construct($this->class, $this->name);
    }
}