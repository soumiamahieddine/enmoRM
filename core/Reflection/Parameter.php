<?php
/**
 * Class file for Reflection Method or Service Parameter
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class that defines a service or method parameter
 *
 * @uses \core\ReadonlyTrait
 */
class Parameter extends \ReflectionParameter
{
    /* Properties */
    /**
     * @var string The type of the param
     */
    public $type;

    /**
     * @var string The documentation
     */
    public $description;

    /* Methods */
    /**
     * Constructor of the injection service method parameter
     * @param string $name   The name of the parameter
     * @param string $method The name of the method
     * @param string $class  The class of the service that declares the method
     * @param string $type   The type of parameter retrieved from method doc comments
     * @param string $doc    The documentation retrieved from method doc comments
     */
    public function __construct($name, $method, $class, $type = null, $doc = false)
    {
        parent::__construct(array($class, $method), $name);

        $this->description = $doc;

        if (version_compare(PHP_VERSION, '8.0.0', '>=') && parent::getType()) {
            $this->type = parent::getType()->getName();
        } else {
            $this->type = $type;

            // Unknown array type
            if (parent::getType() && parent::getType()->getName() === 'array' && !$type) {
                $this->type = 'array';
            }

            // Class
            if (preg_match('/\\[\s\<\w+?>\s([\w\\\\]+)/s', $this->__toString(), $matches)) {
                if (isset($matches[1])) {
                    $this->type = $matches[1];  
                }
            }
        }
    }

    /**
     * Get the type of the parameter
     * @return string A class name, 'array' or null
     */
    public function getType()
    {
        return $this->type;
    }
}
