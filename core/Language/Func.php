<?php

namespace core\Language;

class Func
    extends AbstractOperand
{

    /* Properties */
    public $args;

    /* Methods */
    public function __construct($name, $args)
    {
        $this->code = $code;

        $this->args = $args;
    }

}