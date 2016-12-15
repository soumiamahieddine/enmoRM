<?php
namespace core\Language;
/**
 * Represents a query assert
 */
class Assert
    extends AbstractUnaryOperation
{

    public function __construct($operand)
    {
        parent::__construct(LAABS_T_ASSERT, $operand);
    }

}
