<?php
namespace core\Reflection;
/**
 * Reflection class for components : service, controller, type, parser, serializer, observer
 * 
 * @package Laabs
 */
abstract class abstractMethod
    extends \ReflectionMethod
{

    use \core\ReadonlyTrait,
        DocCommentTrait;

    /**
     * The type of return
     * @var string
     */
    public $returnType;

    /**
     * The parameters
     * @var array
     */
    public $parameters;

    /**
     * Constructor of the injection service
     * @param string $classname  The class for reflection
     * @param string $methodname The name of the method
     */
    public function __construct($classname, $methodname)
    {
        parent::__construct($classname, $methodname);

        $this->parseDocComment();
    }

    /**
     * Called for unserialization
     */
    public function __wakeup() 
    {
        parent::__construct($this->class, $this->name);
    }

    /**
     * Indicates whether the method has parameters or not
     * @return bool
     */
    public function hasParameters()
    {
        if ($this->getNumberOfParameters() > 0) {
            return true;
        }
    }

    /**
     * Get the parameters of the method
     * @return array An array of the Parameter objects for the method
     */
    public function getParameters()
    {
        if (!isset($this->parameters)) {

            $this->parameters = array();
            
            $paramComment = array();
            preg_match_all('#@param (?<type>[^\s]+)\s*(?<name>[^\s]+)(?<doc>.*)#', $this->getDocComment(), $paramComment, PREG_SET_ORDER);
            foreach ((array) parent::getParameters() as $pos => $rParameter) {
                $paramType = null;
                $paramDoc = false;
                if (isset($paramComment[$pos])) {
                    $paramType = $paramComment[$pos]['type'];
                    $paramDoc = trim($paramComment[$pos]['doc']);
                }
                $this->parameters[] = new Parameter($rParameter->name, $this->name, $this->class, $paramType, $paramDoc);
            }
        }

        return $this->parameters;
    }

    /**
     * Get the return type of the method from doc comments
     * 
     * @return string The type
     */
    public function getReturnType()
    {
        $docComment = $this->getDocComment();
        if (preg_match("#@return (?<type>[^\s]+)#", $docComment, $matches)) {
            return trim($matches['type']);
        }
    }

    /**
     * Get the exceptions thrown by the method from doc comments
     *
     * @return string[] The exceptions
     */
    public function getThrownExceptions()
    {
        $docComment = $this->getDocComment();
        if (preg_match_all("#@throws (?<type>[^\s]+)#", $docComment, $matches)) {
            return $matches['type'];
        }
    }  
}
