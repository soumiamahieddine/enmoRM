<?php
namespace core\Language;
/**
 * Represents a binary operator node, a binary operator has a left and a right operand
 */
class LogicalOperation
    extends AbstractBinaryOperation
{
    
    public function __construct($code=LAABS_T_AND, $left=null, $right=null)
    {
        parent::__construct($code, $left, $right);
    }
}
