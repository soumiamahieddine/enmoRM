<?php
namespace core\Language;
/**
 * Represents an operand in an operation
 */
abstract class AbstractOperand
{
    use \core\ReadonlyTrait;
    
    /**
     * The value
     *
     * @var mixed
     */
    public $value;
    
    /**
     * The class constructor
     * @param mixed $value
     */
    public function __construct($value)
    {       
        $this->value = $value;
    }

}
