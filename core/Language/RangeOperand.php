<?php

namespace core\Language;

class RangeOperand
    extends AbstractOperand
{

    /* constants */

    /* Properties */
    public $from;

    public $to;

    /* Methods */
    /**
     * The class constructor
     * @param mixed $from
     * @param mixed $to
     */
    public function __construct($from, $to)
    {       
        $this->from = $from;
        
        $this->to = $to;
    }
}