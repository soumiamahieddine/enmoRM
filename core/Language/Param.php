<?php
namespace core\Language;

class Param
{

    public function __construct($name, &$value=null, $type=null, $length=null)
    {
        $this->name = $name;
        $this->value = &$value;
        $this->type = $type;
        $this->length = $length;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getLength()
    {
        return $this->length;
    }
}