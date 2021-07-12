<?php
namespace core\Language;
/**
 * Represents a binary operator node, a binary operator has a left and a right operand
 */
class ComparisonOperation
    extends AbstractBinaryOperation
{
    
    public function __construct($code=LAABS_T_EQUAL, $left=null, $right=LAABS_T_TRUE)
    {
        parent::__construct($code, $left, $right);
    }
}
