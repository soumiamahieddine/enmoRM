<?php
namespace core\Language;
/**
 * Represents an unary operator node, an unary operator can only have one operand
 */
abstract class AbstractUnaryOperation
{
    use \core\ReadonlyTrait;
    /**
     * The operator
     *
     * @var mixed
     */
    public $code = null;
    /**
     * The operand
     *
     * @var mixed
     */
    public $operand = null;
    /**
     * The class constructor
     *
     * @param mixed $code
     * @param mixed $operand
     */
    public function __construct($code, $operand)
    {
        $this->code = $code;
        $this->operand = $operand;
    }

    /**
     * Get the op code
     * @return mixed The var
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the left op
     * @return mixed The var
     */
    public function getOperand()
    {
        return $this->operand;
    }
}
