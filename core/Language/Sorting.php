<?php

namespace core\Language;

class Sorting
{
    /* constants */

    /* Properties */
    public $property;

    public $order;

    /* Methods */
    public function __construct($property, $order=LAABS_T_ASC)
    {
        $this->property = $property;
        $this->order = $order;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function getOrder()
    {
        return $this->order;
    }

}