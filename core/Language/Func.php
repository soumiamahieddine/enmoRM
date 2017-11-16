<?php

namespace core\Language;

class Func
    extends AbstractOperand
{

    /* Properties */
    public $args;
    public $name;

    /* Methods */
    public function __construct($name, $args)
    {
        $this->name = $name;

        $this->args = $args;
    }

}