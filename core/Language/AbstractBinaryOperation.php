<?php
namespace core\Language;
/**
 * Represents a binary operator node, a binary operator has a left and a right operand
 */
abstract class AbstractBinaryOperation
{
    use \core\ReadonlyTrait;
    /**
     * The operator
     * @var mixed
     */
    public $code;

    /**
     * The left operand
     * @var mixed
     */
    public $left;

    /**
     * The right operand
     * @var mixed
     */
    public $right;

    /**
     * The class constructor
     * @param mixed $code
     * @param mixed $left
     * @param mixed $right
     */
    public function __construct($code, $left, $right)
    {
        $this->code = $code;
        $this->left = $left;
        $this->right = $right;
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
    public function getLeftOperator()
    {
        return $this->left;
    }

    /**
     * Get the right op
     * @return mixed The var
     */
    public function getRightOperator()
    {
        return $this->right;
    }

}
