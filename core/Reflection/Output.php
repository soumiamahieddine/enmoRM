<?php
/**
 * Class file for Output definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Output definitions
 * 
 * @extends \core\Reflection\Method
 */
class Output
    extends Method
{

    /* Constants */

    /* Properties */
    public $bundle;

    public $serializer;
    
    /* Methods */
    /**
     * Constructs a new output instance
     * @param string $name             The name of output
     * @param string $class            The class of the output
     * @param string $serviceContainer The service container name
     */
    public function __construct($name, $class, $serviceContainer)
    {
        parent::__construct($name, $class, $serviceContainer);

        $this->bundle = \laabs\basename(\laabs\dirname(\laabs\dirname($class)));

        $this->serializer = \laabs\basename($class);
    }
    /**
     * Serialize the output
     * @param object $serializer The serializer of the output
     * @param object $data       The data to serialize
     * @param object $language   The language
     * 
     * @return string response body
     */
    public function serialize($serializer = null, $data = null, $language = null)
    {
        $args = array($data, $language);
        \core\Observer\Dispatcher::notify(LAABS_OUTPUT_SERIALIZE, $this, $args);

        $output = $this->invokeArgs($serializer, array($data, $language));

        \core\Observer\Dispatcher::notify(LAABS_OUTPUT_STREAM, $output);

        return $output;
    }

}
